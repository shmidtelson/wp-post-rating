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
use WPR\Vendor\Twig\Node\BodyNode;
use WPR\Vendor\Twig\Node\MacroNode;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Token;
/**
 * Defines a macro.
 *
 *   {% macro input(name, value, type, size) %}
 *      <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
 *   {% endmacro %}
 */
final class MacroTokenParser extends \WPR\Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR\Vendor\Twig\Token $token) : \WPR\Vendor\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = $stream->expect(
            /* Token::NAME_TYPE */
            5
        )->getValue();
        $arguments = $this->parser->getExpressionParser()->parseArguments(\true, \true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $this->parser->pushLocalScope();
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
        if ($token = $stream->nextIf(
            /* Token::NAME_TYPE */
            5
        )) {
            $value = $token->getValue();
            if ($value != $name) {
                throw new \WPR\Vendor\Twig\Error\SyntaxError(\sprintf('Expected endmacro for macro "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }
        $this->parser->popLocalScope();
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $this->parser->setMacro($name, new \WPR\Vendor\Twig\Node\MacroNode($name, new \WPR\Vendor\Twig\Node\BodyNode([$body]), $arguments, $lineno, $this->getTag()));
        return new \WPR\Vendor\Twig\Node\Node();
    }
    public function decideBlockEnd(\WPR\Vendor\Twig\Token $token) : bool
    {
        return $token->test('endmacro');
    }
    public function getTag() : string
    {
        return 'macro';
    }
}
