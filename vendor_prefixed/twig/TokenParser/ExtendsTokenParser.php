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
namespace WPR_Vendor\Twig\TokenParser;

use WPR_Vendor\Twig\Error\SyntaxError;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Token;
/**
 * Extends a template by another one.
 *
 *  {% extends "base.html" %}
 */
final class ExtendsTokenParser extends \WPR_Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR_Vendor\Twig\Token $token) : \WPR_Vendor\Twig\Node\Node
    {
        $stream = $this->parser->getStream();
        if ($this->parser->peekBlockStack()) {
            throw new \WPR_Vendor\Twig\Error\SyntaxError('Cannot use "extend" in a block.', $token->getLine(), $stream->getSourceContext());
        } elseif (!$this->parser->isMainScope()) {
            throw new \WPR_Vendor\Twig\Error\SyntaxError('Cannot use "extend" in a macro.', $token->getLine(), $stream->getSourceContext());
        }
        if (null !== $this->parser->getParent()) {
            throw new \WPR_Vendor\Twig\Error\SyntaxError('Multiple extends tags are forbidden.', $token->getLine(), $stream->getSourceContext());
        }
        $this->parser->setParent($this->parser->getExpressionParser()->parseExpression());
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \WPR_Vendor\Twig\Node\Node();
    }
    public function getTag() : string
    {
        return 'extends';
    }
}
