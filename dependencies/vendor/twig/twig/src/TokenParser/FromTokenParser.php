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

use WPR\Vendor\Twig\Node\Expression\AssignNameExpression;
use WPR\Vendor\Twig\Node\ImportNode;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Token;
/**
 * Imports macros.
 *
 *   {% from 'forms.html' import forms %}
 */
final class FromTokenParser extends \WPR\Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR\Vendor\Twig\Token $token) : \WPR\Vendor\Twig\Node\Node
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::NAME_TYPE */
            5,
            'import'
        );
        $targets = [];
        do {
            $name = $stream->expect(
                /* Token::NAME_TYPE */
                5
            )->getValue();
            $alias = $name;
            if ($stream->nextIf('as')) {
                $alias = $stream->expect(
                    /* Token::NAME_TYPE */
                    5
                )->getValue();
            }
            $targets[$name] = $alias;
            if (!$stream->nextIf(
                /* Token::PUNCTUATION_TYPE */
                9,
                ','
            )) {
                break;
            }
        } while (\true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $var = new \WPR\Vendor\Twig\Node\Expression\AssignNameExpression($this->parser->getVarName(), $token->getLine());
        $node = new \WPR\Vendor\Twig\Node\ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
        foreach ($targets as $name => $alias) {
            $this->parser->addImportedSymbol('function', $alias, 'macro_' . $name, $var);
        }
        return $node;
    }
    public function getTag() : string
    {
        return 'from';
    }
}
