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

use WPR_Vendor\Twig\Node\Expression\AssignNameExpression;
use WPR_Vendor\Twig\Node\ImportNode;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Token;
/**
 * Imports macros.
 *
 *   {% import 'forms.html' as forms %}
 */
final class ImportTokenParser extends \WPR_Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR_Vendor\Twig\Token $token) : \WPR_Vendor\Twig\Node\Node
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(
            /* Token::NAME_TYPE */
            5,
            'as'
        );
        $var = new \WPR_Vendor\Twig\Node\Expression\AssignNameExpression($this->parser->getStream()->expect(
            /* Token::NAME_TYPE */
            5
        )->getValue(), $token->getLine());
        $this->parser->getStream()->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $this->parser->addImportedSymbol('template', $var->getAttribute('name'));
        return new \WPR_Vendor\Twig\Node\ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
    }
    public function getTag() : string
    {
        return 'import';
    }
}
