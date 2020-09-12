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

use WPR\Vendor\Twig\Error\SyntaxError;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Node\SetNode;
use WPR\Vendor\Twig\Token;
/**
 * Defines a variable.
 *
 *  {% set foo = 'foo' %}
 *  {% set foo = [1, 2] %}
 *  {% set foo = {'foo': 'bar'} %}
 *  {% set foo = 'foo' ~ 'bar' %}
 *  {% set foo, bar = 'foo', 'bar' %}
 *  {% set foo %}Some content{% endset %}
 */
final class SetTokenParser extends \WPR\Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR\Vendor\Twig\Token $token) : \WPR\Vendor\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $names = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $capture = \false;
        if ($stream->nextIf(
            /* Token::OPERATOR_TYPE */
            8,
            '='
        )) {
            $values = $this->parser->getExpressionParser()->parseMultitargetExpression();
            $stream->expect(
                /* Token::BLOCK_END_TYPE */
                3
            );
            if (\count($names) !== \count($values)) {
                throw new \WPR\Vendor\Twig\Error\SyntaxError('When using set, you must have the same number of variables and assignments.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        } else {
            $capture = \true;
            if (\count($names) > 1) {
                throw new \WPR\Vendor\Twig\Error\SyntaxError('When using set with a block, you cannot have a multi-target.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
            $stream->expect(
                /* Token::BLOCK_END_TYPE */
                3
            );
            $values = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
            $stream->expect(
                /* Token::BLOCK_END_TYPE */
                3
            );
        }
        return new \WPR\Vendor\Twig\Node\SetNode($capture, $names, $values, $lineno, $this->getTag());
    }
    public function decideBlockEnd(\WPR\Vendor\Twig\Token $token) : bool
    {
        return $token->test('endset');
    }
    public function getTag() : string
    {
        return 'set';
    }
}
