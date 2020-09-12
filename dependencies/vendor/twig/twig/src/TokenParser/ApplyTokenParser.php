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

use WPR\Vendor\Twig\Node\Expression\TempNameExpression;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Node\PrintNode;
use WPR\Vendor\Twig\Node\SetNode;
use WPR\Vendor\Twig\Token;
/**
 * Applies filters on a section of a template.
 *
 *   {% apply upper %}
 *      This text becomes uppercase
 *   {% endapply %}
 */
final class ApplyTokenParser extends \WPR\Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR\Vendor\Twig\Token $token) : \WPR\Vendor\Twig\Node\Node
    {
        $lineno = $token->getLine();
        $name = $this->parser->getVarName();
        $ref = new \WPR\Vendor\Twig\Node\Expression\TempNameExpression($name, $lineno);
        $ref->setAttribute('always_defined', \true);
        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
        $this->parser->getStream()->expect(\WPR\Vendor\Twig\Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideApplyEnd'], \true);
        $this->parser->getStream()->expect(\WPR\Vendor\Twig\Token::BLOCK_END_TYPE);
        return new \WPR\Vendor\Twig\Node\Node([new \WPR\Vendor\Twig\Node\SetNode(\true, $ref, $body, $lineno, $this->getTag()), new \WPR\Vendor\Twig\Node\PrintNode($filter, $lineno, $this->getTag())]);
    }
    public function decideApplyEnd(\WPR\Vendor\Twig\Token $token) : bool
    {
        return $token->test('endapply');
    }
    public function getTag() : string
    {
        return 'apply';
    }
}
