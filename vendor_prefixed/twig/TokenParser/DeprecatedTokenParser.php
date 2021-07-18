<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\TokenParser;

use WPR_Vendor\Twig\Node\DeprecatedNode;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Token;
/**
 * Deprecates a section of a template.
 *
 *    {% deprecated 'The "base.twig" template is deprecated, use "layout.twig" instead.' %}
 *    {% extends 'layout.html.twig' %}
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
final class DeprecatedTokenParser extends AbstractTokenParser
{
    public function parse(Token $token) : Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        return new DeprecatedNode($expr, $token->getLine(), $this->getTag());
    }
    public function getTag() : string
    {
        return 'deprecated';
    }
}
