<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\TokenParser;

use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Node\WithNode;
use WPR\Vendor\Twig\Token;
/**
 * Creates a nested scope.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class WithTokenParser extends \WPR\Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR\Vendor\Twig\Token $token) : \WPR\Vendor\Twig\Node\Node
    {
        $stream = $this->parser->getStream();
        $variables = null;
        $only = \false;
        if (!$stream->test(
            /* Token::BLOCK_END_TYPE */
            3
        )) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
            $only = (bool) $stream->nextIf(
                /* Token::NAME_TYPE */
                5,
                'only'
            );
        }
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $body = $this->parser->subparse([$this, 'decideWithEnd'], \true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \WPR\Vendor\Twig\Node\WithNode($body, $variables, $only, $token->getLine(), $this->getTag());
    }
    public function decideWithEnd(\WPR\Vendor\Twig\Token $token) : bool
    {
        return $token->test('endwith');
    }
    public function getTag() : string
    {
        return 'with';
    }
}
