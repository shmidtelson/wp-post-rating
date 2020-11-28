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
use WPR_Vendor\Twig\Node\Expression\AbstractExpression;
use WPR_Vendor\Twig\Node\Expression\ArrayExpression;
use WPR_Vendor\Twig\Node\Expression\ArrowFunctionExpression;
use WPR_Vendor\Twig\Node\Expression\AssignNameExpression;
use WPR_Vendor\Twig\Node\Expression\Binary\ConcatBinary;
use WPR_Vendor\Twig\Node\Expression\BlockReferenceExpression;
use WPR_Vendor\Twig\Node\Expression\ConditionalExpression;
use WPR_Vendor\Twig\Node\Expression\ConstantExpression;
use WPR_Vendor\Twig\Node\Expression\GetAttrExpression;
use WPR_Vendor\Twig\Node\Expression\MethodCallExpression;
use WPR_Vendor\Twig\Node\Expression\NameExpression;
use WPR_Vendor\Twig\Node\Expression\ParentExpression;
use WPR_Vendor\Twig\Node\Expression\TestExpression;
use WPR_Vendor\Twig\Node\Expression\Unary\NegUnary;
use WPR_Vendor\Twig\Node\Expression\Unary\NotUnary;
use WPR_Vendor\Twig\Node\Expression\Unary\PosUnary;
use WPR_Vendor\Twig\Node\Node;
/**
 * Parses expressions.
 *
 * This parser implements a "Precedence climbing" algorithm.
 *
 * @see https://www.engr.mun.ca/~theo/Misc/exp_parsing.htm
 * @see https://en.wikipedia.org/wiki/Operator-precedence_parser
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
class ExpressionParser
{
    const OPERATOR_LEFT = 1;
    const OPERATOR_RIGHT = 2;
    private $parser;
    private $env;
    private $unaryOperators;
    private $binaryOperators;
    public function __construct(\WPR_Vendor\Twig\Parser $parser, \WPR_Vendor\Twig\Environment $env)
    {
        $this->parser = $parser;
        $this->env = $env;
        $this->unaryOperators = $env->getUnaryOperators();
        $this->binaryOperators = $env->getBinaryOperators();
    }
    public function parseExpression($precedence = 0, $allowArrow = \false)
    {
        if ($allowArrow && ($arrow = $this->parseArrow())) {
            return $arrow;
        }
        $expr = $this->getPrimary();
        $token = $this->parser->getCurrentToken();
        while ($this->isBinary($token) && $this->binaryOperators[$token->getValue()]['precedence'] >= $precedence) {
            $op = $this->binaryOperators[$token->getValue()];
            $this->parser->getStream()->next();
            if ('is not' === $token->getValue()) {
                $expr = $this->parseNotTestExpression($expr);
            } elseif ('is' === $token->getValue()) {
                $expr = $this->parseTestExpression($expr);
            } elseif (isset($op['callable'])) {
                $expr = $op['callable']($this->parser, $expr);
            } else {
                $expr1 = $this->parseExpression(self::OPERATOR_LEFT === $op['associativity'] ? $op['precedence'] + 1 : $op['precedence']);
                $class = $op['class'];
                $expr = new $class($expr, $expr1, $token->getLine());
            }
            $token = $this->parser->getCurrentToken();
        }
        if (0 === $precedence) {
            return $this->parseConditionalExpression($expr);
        }
        return $expr;
    }
    /**
     * @return ArrowFunctionExpression|null
     */
    private function parseArrow()
    {
        $stream = $this->parser->getStream();
        // short array syntax (one argument, no parentheses)?
        if ($stream->look(1)->test(
            /* Token::ARROW_TYPE */
            12
        )) {
            $line = $stream->getCurrent()->getLine();
            $token = $stream->expect(
                /* Token::NAME_TYPE */
                5
            );
            $names = [new \WPR_Vendor\Twig\Node\Expression\AssignNameExpression($token->getValue(), $token->getLine())];
            $stream->expect(
                /* Token::ARROW_TYPE */
                12
            );
            return new \WPR_Vendor\Twig\Node\Expression\ArrowFunctionExpression($this->parseExpression(0), new \WPR_Vendor\Twig\Node\Node($names), $line);
        }
        // first, determine if we are parsing an arrow function by finding => (long form)
        $i = 0;
        if (!$stream->look($i)->test(
            /* Token::PUNCTUATION_TYPE */
            9,
            '('
        )) {
            return null;
        }
        ++$i;
        while (\true) {
            // variable name
            ++$i;
            if (!$stream->look($i)->test(
                /* Token::PUNCTUATION_TYPE */
                9,
                ','
            )) {
                break;
            }
            ++$i;
        }
        if (!$stream->look($i)->test(
            /* Token::PUNCTUATION_TYPE */
            9,
            ')'
        )) {
            return null;
        }
        ++$i;
        if (!$stream->look($i)->test(
            /* Token::ARROW_TYPE */
            12
        )) {
            return null;
        }
        // yes, let's parse it properly
        $token = $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            '('
        );
        $line = $token->getLine();
        $names = [];
        while (\true) {
            $token = $stream->expect(
                /* Token::NAME_TYPE */
                5
            );
            $names[] = new \WPR_Vendor\Twig\Node\Expression\AssignNameExpression($token->getValue(), $token->getLine());
            if (!$stream->nextIf(
                /* Token::PUNCTUATION_TYPE */
                9,
                ','
            )) {
                break;
            }
        }
        $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            ')'
        );
        $stream->expect(
            /* Token::ARROW_TYPE */
            12
        );
        return new \WPR_Vendor\Twig\Node\Expression\ArrowFunctionExpression($this->parseExpression(0), new \WPR_Vendor\Twig\Node\Node($names), $line);
    }
    private function getPrimary() : \WPR_Vendor\Twig\Node\Expression\AbstractExpression
    {
        $token = $this->parser->getCurrentToken();
        if ($this->isUnary($token)) {
            $operator = $this->unaryOperators[$token->getValue()];
            $this->parser->getStream()->next();
            $expr = $this->parseExpression($operator['precedence']);
            $class = $operator['class'];
            return $this->parsePostfixExpression(new $class($expr, $token->getLine()));
        } elseif ($token->test(
            /* Token::PUNCTUATION_TYPE */
            9,
            '('
        )) {
            $this->parser->getStream()->next();
            $expr = $this->parseExpression();
            $this->parser->getStream()->expect(
                /* Token::PUNCTUATION_TYPE */
                9,
                ')',
                'An opened parenthesis is not properly closed'
            );
            return $this->parsePostfixExpression($expr);
        }
        return $this->parsePrimaryExpression();
    }
    private function parseConditionalExpression($expr) : \WPR_Vendor\Twig\Node\Expression\AbstractExpression
    {
        while ($this->parser->getStream()->nextIf(
            /* Token::PUNCTUATION_TYPE */
            9,
            '?'
        )) {
            if (!$this->parser->getStream()->nextIf(
                /* Token::PUNCTUATION_TYPE */
                9,
                ':'
            )) {
                $expr2 = $this->parseExpression();
                if ($this->parser->getStream()->nextIf(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ':'
                )) {
                    $expr3 = $this->parseExpression();
                } else {
                    $expr3 = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression('', $this->parser->getCurrentToken()->getLine());
                }
            } else {
                $expr2 = $expr;
                $expr3 = $this->parseExpression();
            }
            $expr = new \WPR_Vendor\Twig\Node\Expression\ConditionalExpression($expr, $expr2, $expr3, $this->parser->getCurrentToken()->getLine());
        }
        return $expr;
    }
    private function isUnary(\WPR_Vendor\Twig\Token $token) : bool
    {
        return $token->test(
            /* Token::OPERATOR_TYPE */
            8
        ) && isset($this->unaryOperators[$token->getValue()]);
    }
    private function isBinary(\WPR_Vendor\Twig\Token $token) : bool
    {
        return $token->test(
            /* Token::OPERATOR_TYPE */
            8
        ) && isset($this->binaryOperators[$token->getValue()]);
    }
    public function parsePrimaryExpression()
    {
        $token = $this->parser->getCurrentToken();
        switch ($token->getType()) {
            case 5:
                $this->parser->getStream()->next();
                switch ($token->getValue()) {
                    case 'true':
                    case 'TRUE':
                        $node = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression(\true, $token->getLine());
                        break;
                    case 'false':
                    case 'FALSE':
                        $node = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression(\false, $token->getLine());
                        break;
                    case 'none':
                    case 'NONE':
                    case 'null':
                    case 'NULL':
                        $node = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression(null, $token->getLine());
                        break;
                    default:
                        if ('(' === $this->parser->getCurrentToken()->getValue()) {
                            $node = $this->getFunctionNode($token->getValue(), $token->getLine());
                        } else {
                            $node = new \WPR_Vendor\Twig\Node\Expression\NameExpression($token->getValue(), $token->getLine());
                        }
                }
                break;
            case 6:
                $this->parser->getStream()->next();
                $node = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression($token->getValue(), $token->getLine());
                break;
            case 7:
            case 10:
                $node = $this->parseStringExpression();
                break;
            case 8:
                if (\preg_match(\WPR_Vendor\Twig\Lexer::REGEX_NAME, $token->getValue(), $matches) && $matches[0] == $token->getValue()) {
                    // in this context, string operators are variable names
                    $this->parser->getStream()->next();
                    $node = new \WPR_Vendor\Twig\Node\Expression\NameExpression($token->getValue(), $token->getLine());
                    break;
                } elseif (isset($this->unaryOperators[$token->getValue()])) {
                    $class = $this->unaryOperators[$token->getValue()]['class'];
                    if (!\in_array($class, [\WPR_Vendor\Twig\Node\Expression\Unary\NegUnary::class, \WPR_Vendor\Twig\Node\Expression\Unary\PosUnary::class])) {
                        throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unexpected unary operator "%s".', $token->getValue()), $token->getLine(), $this->parser->getStream()->getSourceContext());
                    }
                    $this->parser->getStream()->next();
                    $expr = $this->parsePrimaryExpression();
                    $node = new $class($expr, $token->getLine());
                    break;
                }
            // no break
            default:
                if ($token->test(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    '['
                )) {
                    $node = $this->parseArrayExpression();
                } elseif ($token->test(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    '{'
                )) {
                    $node = $this->parseHashExpression();
                } elseif ($token->test(
                    /* Token::OPERATOR_TYPE */
                    8,
                    '='
                ) && ('==' === $this->parser->getStream()->look(-1)->getValue() || '!=' === $this->parser->getStream()->look(-1)->getValue())) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unexpected operator of value "%s". Did you try to use "===" or "!==" for strict comparison? Use "is same as(value)" instead.', $token->getValue()), $token->getLine(), $this->parser->getStream()->getSourceContext());
                } else {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unexpected token "%s" of value "%s".', \WPR_Vendor\Twig\Token::typeToEnglish($token->getType()), $token->getValue()), $token->getLine(), $this->parser->getStream()->getSourceContext());
                }
        }
        return $this->parsePostfixExpression($node);
    }
    public function parseStringExpression()
    {
        $stream = $this->parser->getStream();
        $nodes = [];
        // a string cannot be followed by another string in a single expression
        $nextCanBeString = \true;
        while (\true) {
            if ($nextCanBeString && ($token = $stream->nextIf(
                /* Token::STRING_TYPE */
                7
            ))) {
                $nodes[] = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression($token->getValue(), $token->getLine());
                $nextCanBeString = \false;
            } elseif ($stream->nextIf(
                /* Token::INTERPOLATION_START_TYPE */
                10
            )) {
                $nodes[] = $this->parseExpression();
                $stream->expect(
                    /* Token::INTERPOLATION_END_TYPE */
                    11
                );
                $nextCanBeString = \true;
            } else {
                break;
            }
        }
        $expr = \array_shift($nodes);
        foreach ($nodes as $node) {
            $expr = new \WPR_Vendor\Twig\Node\Expression\Binary\ConcatBinary($expr, $node, $node->getTemplateLine());
        }
        return $expr;
    }
    public function parseArrayExpression()
    {
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            '[',
            'An array element was expected'
        );
        $node = new \WPR_Vendor\Twig\Node\Expression\ArrayExpression([], $stream->getCurrent()->getLine());
        $first = \true;
        while (!$stream->test(
            /* Token::PUNCTUATION_TYPE */
            9,
            ']'
        )) {
            if (!$first) {
                $stream->expect(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ',',
                    'An array element must be followed by a comma'
                );
                // trailing ,?
                if ($stream->test(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ']'
                )) {
                    break;
                }
            }
            $first = \false;
            $node->addElement($this->parseExpression());
        }
        $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            ']',
            'An opened array is not properly closed'
        );
        return $node;
    }
    public function parseHashExpression()
    {
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            '{',
            'A hash element was expected'
        );
        $node = new \WPR_Vendor\Twig\Node\Expression\ArrayExpression([], $stream->getCurrent()->getLine());
        $first = \true;
        while (!$stream->test(
            /* Token::PUNCTUATION_TYPE */
            9,
            '}'
        )) {
            if (!$first) {
                $stream->expect(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ',',
                    'A hash value must be followed by a comma'
                );
                // trailing ,?
                if ($stream->test(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    '}'
                )) {
                    break;
                }
            }
            $first = \false;
            // a hash key can be:
            //
            //  * a number -- 12
            //  * a string -- 'a'
            //  * a name, which is equivalent to a string -- a
            //  * an expression, which must be enclosed in parentheses -- (1 + 2)
            if ($token = $stream->nextIf(
                /* Token::NAME_TYPE */
                5
            )) {
                $key = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression($token->getValue(), $token->getLine());
                // {a} is a shortcut for {a:a}
                if ($stream->test(\WPR_Vendor\Twig\Token::PUNCTUATION_TYPE, [',', '}'])) {
                    $value = new \WPR_Vendor\Twig\Node\Expression\NameExpression($key->getAttribute('value'), $key->getTemplateLine());
                    $node->addElement($value, $key);
                    continue;
                }
            } elseif (($token = $stream->nextIf(
                /* Token::STRING_TYPE */
                7
            )) || ($token = $stream->nextIf(
                /* Token::NUMBER_TYPE */
                6
            ))) {
                $key = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression($token->getValue(), $token->getLine());
            } elseif ($stream->test(
                /* Token::PUNCTUATION_TYPE */
                9,
                '('
            )) {
                $key = $this->parseExpression();
            } else {
                $current = $stream->getCurrent();
                throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('A hash key must be a quoted string, a number, a name, or an expression enclosed in parentheses (unexpected token "%s" of value "%s".', \WPR_Vendor\Twig\Token::typeToEnglish($current->getType()), $current->getValue()), $current->getLine(), $stream->getSourceContext());
            }
            $stream->expect(
                /* Token::PUNCTUATION_TYPE */
                9,
                ':',
                'A hash key must be followed by a colon (:)'
            );
            $value = $this->parseExpression();
            $node->addElement($value, $key);
        }
        $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            '}',
            'An opened hash is not properly closed'
        );
        return $node;
    }
    public function parsePostfixExpression($node)
    {
        while (\true) {
            $token = $this->parser->getCurrentToken();
            if (9 == $token->getType()) {
                if ('.' == $token->getValue() || '[' == $token->getValue()) {
                    $node = $this->parseSubscriptExpression($node);
                } elseif ('|' == $token->getValue()) {
                    $node = $this->parseFilterExpression($node);
                } else {
                    break;
                }
            } else {
                break;
            }
        }
        return $node;
    }
    public function getFunctionNode($name, $line)
    {
        switch ($name) {
            case 'parent':
                $this->parseArguments();
                if (!\count($this->parser->getBlockStack())) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError('Calling "parent" outside a block is forbidden.', $line, $this->parser->getStream()->getSourceContext());
                }
                if (!$this->parser->getParent() && !$this->parser->hasTraits()) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError('Calling "parent" on a template that does not extend nor "use" another template is forbidden.', $line, $this->parser->getStream()->getSourceContext());
                }
                return new \WPR_Vendor\Twig\Node\Expression\ParentExpression($this->parser->peekBlockStack(), $line);
            case 'block':
                $args = $this->parseArguments();
                if (\count($args) < 1) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError('The "block" function takes one argument (the block name).', $line, $this->parser->getStream()->getSourceContext());
                }
                return new \WPR_Vendor\Twig\Node\Expression\BlockReferenceExpression($args->getNode(0), \count($args) > 1 ? $args->getNode(1) : null, $line);
            case 'attribute':
                $args = $this->parseArguments();
                if (\count($args) < 2) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError('The "attribute" function takes at least two arguments (the variable and the attributes).', $line, $this->parser->getStream()->getSourceContext());
                }
                return new \WPR_Vendor\Twig\Node\Expression\GetAttrExpression($args->getNode(0), $args->getNode(1), \count($args) > 2 ? $args->getNode(2) : null, \WPR_Vendor\Twig\Template::ANY_CALL, $line);
            default:
                if (null !== ($alias = $this->parser->getImportedSymbol('function', $name))) {
                    $arguments = new \WPR_Vendor\Twig\Node\Expression\ArrayExpression([], $line);
                    foreach ($this->parseArguments() as $n) {
                        $arguments->addElement($n);
                    }
                    $node = new \WPR_Vendor\Twig\Node\Expression\MethodCallExpression($alias['node'], $alias['name'], $arguments, $line);
                    $node->setAttribute('safe', \true);
                    return $node;
                }
                $args = $this->parseArguments(\true);
                $class = $this->getFunctionNodeClass($name, $line);
                return new $class($name, $args, $line);
        }
    }
    public function parseSubscriptExpression($node)
    {
        $stream = $this->parser->getStream();
        $token = $stream->next();
        $lineno = $token->getLine();
        $arguments = new \WPR_Vendor\Twig\Node\Expression\ArrayExpression([], $lineno);
        $type = \WPR_Vendor\Twig\Template::ANY_CALL;
        if ('.' == $token->getValue()) {
            $token = $stream->next();
            if (5 == $token->getType() || 6 == $token->getType() || 8 == $token->getType() && \preg_match(\WPR_Vendor\Twig\Lexer::REGEX_NAME, $token->getValue())) {
                $arg = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression($token->getValue(), $lineno);
                if ($stream->test(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    '('
                )) {
                    $type = \WPR_Vendor\Twig\Template::METHOD_CALL;
                    foreach ($this->parseArguments() as $n) {
                        $arguments->addElement($n);
                    }
                }
            } else {
                throw new \WPR_Vendor\Twig\Error\SyntaxError('Expected name or number.', $lineno, $stream->getSourceContext());
            }
            if ($node instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression && null !== $this->parser->getImportedSymbol('template', $node->getAttribute('name'))) {
                if (!$arg instanceof \WPR_Vendor\Twig\Node\Expression\ConstantExpression) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Dynamic macro names are not supported (called on "%s").', $node->getAttribute('name')), $token->getLine(), $stream->getSourceContext());
                }
                $name = $arg->getAttribute('value');
                $node = new \WPR_Vendor\Twig\Node\Expression\MethodCallExpression($node, 'macro_' . $name, $arguments, $lineno);
                $node->setAttribute('safe', \true);
                return $node;
            }
        } else {
            $type = \WPR_Vendor\Twig\Template::ARRAY_CALL;
            // slice?
            $slice = \false;
            if ($stream->test(
                /* Token::PUNCTUATION_TYPE */
                9,
                ':'
            )) {
                $slice = \true;
                $arg = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression(0, $token->getLine());
            } else {
                $arg = $this->parseExpression();
            }
            if ($stream->nextIf(
                /* Token::PUNCTUATION_TYPE */
                9,
                ':'
            )) {
                $slice = \true;
            }
            if ($slice) {
                if ($stream->test(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ']'
                )) {
                    $length = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression(null, $token->getLine());
                } else {
                    $length = $this->parseExpression();
                }
                $class = $this->getFilterNodeClass('slice', $token->getLine());
                $arguments = new \WPR_Vendor\Twig\Node\Node([$arg, $length]);
                $filter = new $class($node, new \WPR_Vendor\Twig\Node\Expression\ConstantExpression('slice', $token->getLine()), $arguments, $token->getLine());
                $stream->expect(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ']'
                );
                return $filter;
            }
            $stream->expect(
                /* Token::PUNCTUATION_TYPE */
                9,
                ']'
            );
        }
        return new \WPR_Vendor\Twig\Node\Expression\GetAttrExpression($node, $arg, $arguments, $type, $lineno);
    }
    public function parseFilterExpression($node)
    {
        $this->parser->getStream()->next();
        return $this->parseFilterExpressionRaw($node);
    }
    public function parseFilterExpressionRaw($node, $tag = null)
    {
        while (\true) {
            $token = $this->parser->getStream()->expect(
                /* Token::NAME_TYPE */
                5
            );
            $name = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression($token->getValue(), $token->getLine());
            if (!$this->parser->getStream()->test(
                /* Token::PUNCTUATION_TYPE */
                9,
                '('
            )) {
                $arguments = new \WPR_Vendor\Twig\Node\Node();
            } else {
                $arguments = $this->parseArguments(\true, \false, \true);
            }
            $class = $this->getFilterNodeClass($name->getAttribute('value'), $token->getLine());
            $node = new $class($node, $name, $arguments, $token->getLine(), $tag);
            if (!$this->parser->getStream()->test(
                /* Token::PUNCTUATION_TYPE */
                9,
                '|'
            )) {
                break;
            }
            $this->parser->getStream()->next();
        }
        return $node;
    }
    /**
     * Parses arguments.
     *
     * @param bool $namedArguments Whether to allow named arguments or not
     * @param bool $definition     Whether we are parsing arguments for a function definition
     *
     * @return Node
     *
     * @throws SyntaxError
     */
    public function parseArguments($namedArguments = \false, $definition = \false, $allowArrow = \false)
    {
        $args = [];
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            '(',
            'A list of arguments must begin with an opening parenthesis'
        );
        while (!$stream->test(
            /* Token::PUNCTUATION_TYPE */
            9,
            ')'
        )) {
            if (!empty($args)) {
                $stream->expect(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ',',
                    'Arguments must be separated by a comma'
                );
                // if the comma above was a trailing comma, early exit the argument parse loop
                if ($stream->test(
                    /* Token::PUNCTUATION_TYPE */
                    9,
                    ')'
                )) {
                    break;
                }
            }
            if ($definition) {
                $token = $stream->expect(
                    /* Token::NAME_TYPE */
                    5,
                    null,
                    'An argument must be a name'
                );
                $value = new \WPR_Vendor\Twig\Node\Expression\NameExpression($token->getValue(), $this->parser->getCurrentToken()->getLine());
            } else {
                $value = $this->parseExpression(0, $allowArrow);
            }
            $name = null;
            if ($namedArguments && ($token = $stream->nextIf(
                /* Token::OPERATOR_TYPE */
                8,
                '='
            ))) {
                if (!$value instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('A parameter name must be a string, "%s" given.', \get_class($value)), $token->getLine(), $stream->getSourceContext());
                }
                $name = $value->getAttribute('name');
                if ($definition) {
                    $value = $this->parsePrimaryExpression();
                    if (!$this->checkConstantExpression($value)) {
                        throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('A default value for an argument must be a constant (a boolean, a string, a number, or an array).'), $token->getLine(), $stream->getSourceContext());
                    }
                } else {
                    $value = $this->parseExpression(0, $allowArrow);
                }
            }
            if ($definition) {
                if (null === $name) {
                    $name = $value->getAttribute('name');
                    $value = new \WPR_Vendor\Twig\Node\Expression\ConstantExpression(null, $this->parser->getCurrentToken()->getLine());
                }
                $args[$name] = $value;
            } else {
                if (null === $name) {
                    $args[] = $value;
                } else {
                    $args[$name] = $value;
                }
            }
        }
        $stream->expect(
            /* Token::PUNCTUATION_TYPE */
            9,
            ')',
            'A list of arguments must be closed by a parenthesis'
        );
        return new \WPR_Vendor\Twig\Node\Node($args);
    }
    public function parseAssignmentExpression()
    {
        $stream = $this->parser->getStream();
        $targets = [];
        while (\true) {
            $token = $this->parser->getCurrentToken();
            if ($stream->test(
                /* Token::OPERATOR_TYPE */
                8
            ) && \preg_match(\WPR_Vendor\Twig\Lexer::REGEX_NAME, $token->getValue())) {
                // in this context, string operators are variable names
                $this->parser->getStream()->next();
            } else {
                $stream->expect(
                    /* Token::NAME_TYPE */
                    5,
                    null,
                    'Only variables can be assigned to'
                );
            }
            $value = $token->getValue();
            if (\in_array(\strtr($value, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), ['true', 'false', 'none', 'null'])) {
                throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('You cannot assign a value to "%s".', $value), $token->getLine(), $stream->getSourceContext());
            }
            $targets[] = new \WPR_Vendor\Twig\Node\Expression\AssignNameExpression($value, $token->getLine());
            if (!$stream->nextIf(
                /* Token::PUNCTUATION_TYPE */
                9,
                ','
            )) {
                break;
            }
        }
        return new \WPR_Vendor\Twig\Node\Node($targets);
    }
    public function parseMultitargetExpression()
    {
        $targets = [];
        while (\true) {
            $targets[] = $this->parseExpression();
            if (!$this->parser->getStream()->nextIf(
                /* Token::PUNCTUATION_TYPE */
                9,
                ','
            )) {
                break;
            }
        }
        return new \WPR_Vendor\Twig\Node\Node($targets);
    }
    private function parseNotTestExpression(\WPR_Vendor\Twig\Node\Node $node) : \WPR_Vendor\Twig\Node\Expression\Unary\NotUnary
    {
        return new \WPR_Vendor\Twig\Node\Expression\Unary\NotUnary($this->parseTestExpression($node), $this->parser->getCurrentToken()->getLine());
    }
    private function parseTestExpression(\WPR_Vendor\Twig\Node\Node $node) : \WPR_Vendor\Twig\Node\Expression\TestExpression
    {
        $stream = $this->parser->getStream();
        list($name, $test) = $this->getTest($node->getTemplateLine());
        $class = $this->getTestNodeClass($test);
        $arguments = null;
        if ($stream->test(
            /* Token::PUNCTUATION_TYPE */
            9,
            '('
        )) {
            $arguments = $this->parseArguments(\true);
        } elseif ($test->hasOneMandatoryArgument()) {
            $arguments = new \WPR_Vendor\Twig\Node\Node([0 => $this->parsePrimaryExpression()]);
        }
        if ('defined' === $name && $node instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression && null !== ($alias = $this->parser->getImportedSymbol('function', $node->getAttribute('name')))) {
            $node = new \WPR_Vendor\Twig\Node\Expression\MethodCallExpression($alias['node'], $alias['name'], new \WPR_Vendor\Twig\Node\Expression\ArrayExpression([], $node->getTemplateLine()), $node->getTemplateLine());
            $node->setAttribute('safe', \true);
        }
        return new $class($node, $name, $arguments, $this->parser->getCurrentToken()->getLine());
    }
    private function getTest(int $line) : array
    {
        $stream = $this->parser->getStream();
        $name = $stream->expect(
            /* Token::NAME_TYPE */
            5
        )->getValue();
        if ($test = $this->env->getTest($name)) {
            return [$name, $test];
        }
        if ($stream->test(
            /* Token::NAME_TYPE */
            5
        )) {
            // try 2-words tests
            $name = $name . ' ' . $this->parser->getCurrentToken()->getValue();
            if ($test = $this->env->getTest($name)) {
                $stream->next();
                return [$name, $test];
            }
        }
        $e = new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unknown "%s" test.', $name), $line, $stream->getSourceContext());
        $e->addSuggestions($name, \array_keys($this->env->getTests()));
        throw $e;
    }
    private function getTestNodeClass(\WPR_Vendor\Twig\TwigTest $test) : string
    {
        if ($test->isDeprecated()) {
            $stream = $this->parser->getStream();
            $message = \sprintf('Twig Test "%s" is deprecated', $test->getName());
            if ($test->getDeprecatedVersion()) {
                $message .= \sprintf(' since version %s', $test->getDeprecatedVersion());
            }
            if ($test->getAlternative()) {
                $message .= \sprintf('. Use "%s" instead', $test->getAlternative());
            }
            $src = $stream->getSourceContext();
            $message .= \sprintf(' in %s at line %d.', $src->getPath() ?: $src->getName(), $stream->getCurrent()->getLine());
            @\trigger_error($message, \E_USER_DEPRECATED);
        }
        return $test->getNodeClass();
    }
    private function getFunctionNodeClass(string $name, int $line) : string
    {
        if (!($function = $this->env->getFunction($name))) {
            $e = new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unknown "%s" function.', $name), $line, $this->parser->getStream()->getSourceContext());
            $e->addSuggestions($name, \array_keys($this->env->getFunctions()));
            throw $e;
        }
        if ($function->isDeprecated()) {
            $message = \sprintf('Twig Function "%s" is deprecated', $function->getName());
            if ($function->getDeprecatedVersion()) {
                $message .= \sprintf(' since version %s', $function->getDeprecatedVersion());
            }
            if ($function->getAlternative()) {
                $message .= \sprintf('. Use "%s" instead', $function->getAlternative());
            }
            $src = $this->parser->getStream()->getSourceContext();
            $message .= \sprintf(' in %s at line %d.', $src->getPath() ?: $src->getName(), $line);
            @\trigger_error($message, \E_USER_DEPRECATED);
        }
        return $function->getNodeClass();
    }
    private function getFilterNodeClass(string $name, int $line) : string
    {
        if (!($filter = $this->env->getFilter($name))) {
            $e = new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unknown "%s" filter.', $name), $line, $this->parser->getStream()->getSourceContext());
            $e->addSuggestions($name, \array_keys($this->env->getFilters()));
            throw $e;
        }
        if ($filter->isDeprecated()) {
            $message = \sprintf('Twig Filter "%s" is deprecated', $filter->getName());
            if ($filter->getDeprecatedVersion()) {
                $message .= \sprintf(' since version %s', $filter->getDeprecatedVersion());
            }
            if ($filter->getAlternative()) {
                $message .= \sprintf('. Use "%s" instead', $filter->getAlternative());
            }
            $src = $this->parser->getStream()->getSourceContext();
            $message .= \sprintf(' in %s at line %d.', $src->getPath() ?: $src->getName(), $line);
            @\trigger_error($message, \E_USER_DEPRECATED);
        }
        return $filter->getNodeClass();
    }
    // checks that the node only contains "constant" elements
    private function checkConstantExpression(\WPR_Vendor\Twig\Node\Node $node) : bool
    {
        if (!($node instanceof \WPR_Vendor\Twig\Node\Expression\ConstantExpression || $node instanceof \WPR_Vendor\Twig\Node\Expression\ArrayExpression || $node instanceof \WPR_Vendor\Twig\Node\Expression\Unary\NegUnary || $node instanceof \WPR_Vendor\Twig\Node\Expression\Unary\PosUnary)) {
            return \false;
        }
        foreach ($node as $n) {
            if (!$this->checkConstantExpression($n)) {
                return \false;
            }
        }
        return \true;
    }
}
