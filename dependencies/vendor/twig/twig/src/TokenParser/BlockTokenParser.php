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
namespace WPR\Vendor\Twig\TokenParser;

use WPR\Vendor\Twig\Error\SyntaxError;
use WPR\Vendor\Twig\Node\BlockNode;
use WPR\Vendor\Twig\Node\BlockReferenceNode;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Node\PrintNode;
use WPR\Vendor\Twig\Token;
/**
 * Marks a section of a template as being reusable.
 *
 *  {% block head %}
 *    <link rel="stylesheet" href="style.css" />
 *    <title>{% block title %}{% endblock %} - My Webpage</title>
 *  {% endblock %}
 */
final class BlockTokenParser extends \WPR\Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR\Vendor\Twig\Token $token) : \WPR\Vendor\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = $stream->expect(
            /* Token::NAME_TYPE */
            5
        )->getValue();
        if ($this->parser->hasBlock($name)) {
            throw new \WPR\Vendor\Twig\Error\SyntaxError(\sprintf("The block '%s' has already been defined line %d.", $name, $this->parser->getBlock($name)->getTemplateLine()), $stream->getCurrent()->getLine(), $stream->getSourceContext());
        }
        $this->parser->setBlock($name, $block = new \WPR\Vendor\Twig\Node\BlockNode($name, new \WPR\Vendor\Twig\Node\Node([]), $lineno));
        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);
        if ($stream->nextIf(
            /* Token::BLOCK_END_TYPE */
            3
        )) {
            $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
            if ($token = $stream->nextIf(
                /* Token::NAME_TYPE */
                5
            )) {
                $value = $token->getValue();
                if ($value != $name) {
                    throw new \WPR\Vendor\Twig\Error\SyntaxError(\sprintf('Expected endblock for block "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext());
                }
            }
        } else {
            $body = new \WPR\Vendor\Twig\Node\Node([new \WPR\Vendor\Twig\Node\PrintNode($this->parser->getExpressionParser()->parseExpression(), $lineno)]);
        }
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $block->setNode('body', $body);
        $this->parser->popBlockStack();
        $this->parser->popLocalScope();
        return new \WPR\Vendor\Twig\Node\BlockReferenceNode($name, $lineno, $this->getTag());
    }
    public function decideBlockEnd(\WPR\Vendor\Twig\Token $token) : bool
    {
        return $token->test('endblock');
    }
    public function getTag() : string
    {
        return 'block';
    }
}
