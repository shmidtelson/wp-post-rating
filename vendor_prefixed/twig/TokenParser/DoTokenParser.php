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

use WPR_Vendor\Twig\Node\DoNode;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Token;
/**
 * Evaluates an expression, discarding the returned value.
 */
final class DoTokenParser extends \WPR_Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR_Vendor\Twig\Token $token) : \WPR_Vendor\Twig\Node\Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \WPR_Vendor\Twig\Node\DoNode($expr, $token->getLine(), $this->getTag());
    }
    public function getTag() : string
    {
        return 'do';
    }
}
