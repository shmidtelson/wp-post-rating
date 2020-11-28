<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig;

use WPR_Vendor\Twig\Error\SyntaxError;
use WPR_Vendor\Twig\Node\BlockNode;
use WPR_Vendor\Twig\Node\BlockReferenceNode;
use WPR_Vendor\Twig\Node\BodyNode;
use WPR_Vendor\Twig\Node\Expression\AbstractExpression;
use WPR_Vendor\Twig\Node\MacroNode;
use WPR_Vendor\Twig\Node\ModuleNode;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Node\NodeCaptureInterface;
use WPR_Vendor\Twig\Node\NodeOutputInterface;
use WPR_Vendor\Twig\Node\PrintNode;
use WPR_Vendor\Twig\Node\TextNode;
use WPR_Vendor\Twig\TokenParser\TokenParserInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Parser
{
    private $stack = [];
    private $stream;
    private $parent;
    private $handlers;
    private $visitors;
    private $expressionParser;
    private $blocks;
    private $blockStack;
    private $macros;
    private $env;
    private $importedSymbols;
    private $traits;
    private $embeddedTemplates = [];
    private $varNameSalt = 0;
    public function __construct(\WPR_Vendor\Twig\Environment $env)
    {
        $this->env = $env;
    }
    public function getVarName() : string
    {
        return \sprintf('__internal_%s', \hash('sha256', __METHOD__ . $this->stream->getSourceContext()->getCode() . $this->varNameSalt++));
    }
    public function parse(\WPR_Vendor\Twig\TokenStream $stream, $test = null, bool $dropNeedle = \false) : \WPR_Vendor\Twig\Node\ModuleNode
    {
        $vars = \get_object_vars($this);
        unset($vars['stack'], $vars['env'], $vars['handlers'], $vars['visitors'], $vars['expressionParser'], $vars['reservedMacroNames']);
        $this->stack[] = $vars;
        // tag handlers
        if (null === $this->handlers) {
            $this->handlers = [];
            foreach ($this->env->getTokenParsers() as $handler) {
                $handler->setParser($this);
                $this->handlers[$handler->getTag()] = $handler;
            }
        }
        // node visitors
        if (null === $this->visitors) {
            $this->visitors = $this->env->getNodeVisitors();
        }
        if (null === $this->expressionParser) {
            $this->expressionParser = new \WPR_Vendor\Twig\ExpressionParser($this, $this->env);
        }
        $this->stream = $stream;
        $this->parent = null;
        $this->blocks = [];
        $this->macros = [];
        $this->traits = [];
        $this->blockStack = [];
        $this->importedSymbols = [[]];
        $this->embeddedTemplates = [];
        $this->varNameSalt = 0;
        try {
            $body = $this->subparse($test, $dropNeedle);
            if (null !== $this->parent && null === ($body = $this->filterBodyNodes($body))) {
                $body = new \WPR_Vendor\Twig\Node\Node();
            }
        } catch (\WPR_Vendor\Twig\Error\SyntaxError $e) {
            if (!$e->getSourceContext()) {
                $e->setSourceContext($this->stream->getSourceContext());
            }
            if (!$e->getTemplateLine()) {
                $e->setTemplateLine($this->stream->getCurrent()->getLine());
            }
            throw $e;
        }
        $node = new \WPR_Vendor\Twig\Node\ModuleNode(new \WPR_Vendor\Twig\Node\BodyNode([$body]), $this->parent, new \WPR_Vendor\Twig\Node\Node($this->blocks), new \WPR_Vendor\Twig\Node\Node($this->macros), new \WPR_Vendor\Twig\Node\Node($this->traits), $this->embeddedTemplates, $stream->getSourceContext());
        $traverser = new \WPR_Vendor\Twig\NodeTraverser($this->env, $this->visitors);
        $node = $traverser->traverse($node);
        // restore previous stack so previous parse() call can resume working
        foreach (\array_pop($this->stack) as $key => $val) {
            $this->{$key} = $val;
        }
        return $node;
    }
    public function subparse($test, bool $dropNeedle = \false) : \WPR_Vendor\Twig\Node\Node
    {
        $lineno = $this->getCurrentToken()->getLine();
        $rv = [];
        while (!$this->stream->isEOF()) {
            switch ($this->getCurrentToken()->getType()) {
                case 0:
                    $token = $this->stream->next();
                    $rv[] = new \WPR_Vendor\Twig\Node\TextNode($token->getValue(), $token->getLine());
                    break;
                case 2:
                    $token = $this->stream->next();
                    $expr = $this->expressionParser->parseExpression();
                    $this->stream->expect(
                        /* Token::VAR_END_TYPE */
                        4
                    );
                    $rv[] = new \WPR_Vendor\Twig\Node\PrintNode($expr, $token->getLine());
                    break;
                case 1:
                    $this->stream->next();
                    $token = $this->getCurrentToken();
                    if (5 !== $token->getType()) {
                        throw new \WPR_Vendor\Twig\Error\SyntaxError('A block must start with a tag name.', $token->getLine(), $this->stream->getSourceContext());
                    }
                    if (null !== $test && $test($token)) {
                        if ($dropNeedle) {
                            $this->stream->next();
                        }
                        if (1 === \count($rv)) {
                            return $rv[0];
                        }
                        return new \WPR_Vendor\Twig\Node\Node($rv, [], $lineno);
                    }
                    if (!isset($this->handlers[$token->getValue()])) {
                        if (null !== $test) {
                            $e = new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unexpected "%s" tag', $token->getValue()), $token->getLine(), $this->stream->getSourceContext());
                            if (\is_array($test) && isset($test[0]) && $test[0] instanceof \WPR_Vendor\Twig\TokenParser\TokenParserInterface) {
                                $e->appendMessage(\sprintf(' (expecting closing tag for the "%s" tag defined near line %s).', $test[0]->getTag(), $lineno));
                            }
                        } else {
                            $e = new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unknown "%s" tag.', $token->getValue()), $token->getLine(), $this->stream->getSourceContext());
                            $e->addSuggestions($token->getValue(), \array_keys($this->env->getTags()));
                        }
                        throw $e;
                    }
                    $this->stream->next();
                    $subparser = $this->handlers[$token->getValue()];
                    $node = $subparser->parse($token);
                    if (null !== $node) {
                        $rv[] = $node;
                    }
                    break;
                default:
                    throw new \WPR_Vendor\Twig\Error\SyntaxError('Lexer or parser ended up in unsupported state.', $this->getCurrentToken()->getLine(), $this->stream->getSourceContext());
            }
        }
        if (1 === \count($rv)) {
            return $rv[0];
        }
        return new \WPR_Vendor\Twig\Node\Node($rv, [], $lineno);
    }
    public function getBlockStack() : array
    {
        return $this->blockStack;
    }
    public function peekBlockStack()
    {
        return isset($this->blockStack[\count($this->blockStack) - 1]) ? $this->blockStack[\count($this->blockStack) - 1] : null;
    }
    public function popBlockStack() : void
    {
        \array_pop($this->blockStack);
    }
    public function pushBlockStack($name) : void
    {
        $this->blockStack[] = $name;
    }
    public function hasBlock(string $name) : bool
    {
        return isset($this->blocks[$name]);
    }
    public function getBlock(string $name) : \WPR_Vendor\Twig\Node\Node
    {
        return $this->blocks[$name];
    }
    public function setBlock(string $name, \WPR_Vendor\Twig\Node\BlockNode $value) : void
    {
        $this->blocks[$name] = new \WPR_Vendor\Twig\Node\BodyNode([$value], [], $value->getTemplateLine());
    }
    public function hasMacro(string $name) : bool
    {
        return isset($this->macros[$name]);
    }
    public function setMacro(string $name, \WPR_Vendor\Twig\Node\MacroNode $node) : void
    {
        $this->macros[$name] = $node;
    }
    public function addTrait($trait) : void
    {
        $this->traits[] = $trait;
    }
    public function hasTraits() : bool
    {
        return \count($this->traits) > 0;
    }
    public function embedTemplate(\WPR_Vendor\Twig\Node\ModuleNode $template)
    {
        $template->setIndex(\mt_rand());
        $this->embeddedTemplates[] = $template;
    }
    public function addImportedSymbol(string $type, string $alias, string $name = null, \WPR_Vendor\Twig\Node\Expression\AbstractExpression $node = null) : void
    {
        $this->importedSymbols[0][$type][$alias] = ['name' => $name, 'node' => $node];
    }
    public function getImportedSymbol(string $type, string $alias)
    {
        // if the symbol does not exist in the current scope (0), try in the main/global scope (last index)
        return $this->importedSymbols[0][$type][$alias] ?? $this->importedSymbols[\count($this->importedSymbols) - 1][$type][$alias] ?? null;
    }
    public function isMainScope() : bool
    {
        return 1 === \count($this->importedSymbols);
    }
    public function pushLocalScope() : void
    {
        \array_unshift($this->importedSymbols, []);
    }
    public function popLocalScope() : void
    {
        \array_shift($this->importedSymbols);
    }
    public function getExpressionParser() : \WPR_Vendor\Twig\ExpressionParser
    {
        return $this->expressionParser;
    }
    public function getParent() : ?\WPR_Vendor\Twig\Node\Node
    {
        return $this->parent;
    }
    public function setParent(?\WPR_Vendor\Twig\Node\Node $parent) : void
    {
        $this->parent = $parent;
    }
    public function getStream() : \WPR_Vendor\Twig\TokenStream
    {
        return $this->stream;
    }
    public function getCurrentToken() : \WPR_Vendor\Twig\Token
    {
        return $this->stream->getCurrent();
    }
    private function filterBodyNodes(\WPR_Vendor\Twig\Node\Node $node, bool $nested = \false) : ?\WPR_Vendor\Twig\Node\Node
    {
        // check that the body does not contain non-empty output nodes
        if ($node instanceof \WPR_Vendor\Twig\Node\TextNode && !\ctype_space($node->getAttribute('data')) || !$node instanceof \WPR_Vendor\Twig\Node\TextNode && !$node instanceof \WPR_Vendor\Twig\Node\BlockReferenceNode && $node instanceof \WPR_Vendor\Twig\Node\NodeOutputInterface) {
            if (\false !== \strpos((string) $node, \chr(0xef) . \chr(0xbb) . \chr(0xbf))) {
                $t = \substr($node->getAttribute('data'), 3);
                if ('' === $t || \ctype_space($t)) {
                    // bypass empty nodes starting with a BOM
                    return null;
                }
            }
            throw new \WPR_Vendor\Twig\Error\SyntaxError('A template that extends another one cannot include content outside Twig blocks. Did you forget to put the content inside a {% block %} tag?', $node->getTemplateLine(), $this->stream->getSourceContext());
        }
        // bypass nodes that "capture" the output
        if ($node instanceof \WPR_Vendor\Twig\Node\NodeCaptureInterface) {
            // a "block" tag in such a node will serve as a block definition AND be displayed in place as well
            return $node;
        }
        // "block" tags that are not captured (see above) are only used for defining
        // the content of the block. In such a case, nesting it does not work as
        // expected as the definition is not part of the default template code flow.
        if ($nested && $node instanceof \WPR_Vendor\Twig\Node\BlockReferenceNode) {
            throw new \WPR_Vendor\Twig\Error\SyntaxError('A block definition cannot be nested under non-capturing nodes.', $node->getTemplateLine(), $this->stream->getSourceContext());
        }
        if ($node instanceof \WPR_Vendor\Twig\Node\NodeOutputInterface) {
            return null;
        }
        // here, $nested means "being at the root level of a child template"
        // we need to discard the wrapping "Node" for the "body" node
        $nested = $nested || \WPR_Vendor\Twig\Node\Node::class !== \get_class($node);
        foreach ($node as $k => $n) {
            if (null !== $n && null === $this->filterBodyNodes($n, $nested)) {
                $node->removeNode($k);
            }
        }
        return $node;
    }
}
