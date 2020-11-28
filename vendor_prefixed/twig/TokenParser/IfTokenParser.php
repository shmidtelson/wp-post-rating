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
use WPR_Vendor\Twig\Node\IfNode;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Token;
/**
 * Tests a condition.
 *
 *   {% if users %}
 *    <ul>
 *      {% for user in users %}
 *        <li>{{ user.username|e }}</li>
 *      {% endfor %}
 *    </ul>
 *   {% endif %}
 */
final class IfTokenParser extends \WPR_Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR_Vendor\Twig\Token $token) : \WPR_Vendor\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $body = $this->parser->subparse([$this, 'decideIfFork']);
        $tests = [$expr, $body];
        $else = null;
        $end = \false;
        while (!$end) {
            switch ($stream->next()->getValue()) {
                case 'else':
                    $stream->expect(
                        /* Token::BLOCK_END_TYPE */
                        3
                    );
                    $else = $this->parser->subparse([$this, 'decideIfEnd']);
                    break;
                case 'elseif':
                    $expr = $this->parser->getExpressionParser()->parseExpression();
                    $stream->expect(
                        /* Token::BLOCK_END_TYPE */
                        3
                    );
                    $body = $this->parser->subparse([$this, 'decideIfFork']);
                    $tests[] = $expr;
                    $tests[] = $body;
                    break;
                case 'endif':
                    $end = \true;
                    break;
                default:
                    throw new \WPR_Vendor\Twig\Error\SyntaxError(\sprintf('Unexpected end of template. Twig was looking for the following tags "else", "elseif", or "endif" to close the "if" block started at line %d).', $lineno), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new \WPR_Vendor\Twig\Node\IfNode(new \WPR_Vendor\Twig\Node\Node($tests), $else, $lineno, $this->getTag());
    }
    public function decideIfFork(\WPR_Vendor\Twig\Token $token) : bool
    {
        return $token->test(['elseif', 'else', 'endif']);
    }
    public function decideIfEnd(\WPR_Vendor\Twig\Token $token) : bool
    {
        return $token->test(['endif']);
    }
    public function getTag() : string
    {
        return 'if';
    }
}
