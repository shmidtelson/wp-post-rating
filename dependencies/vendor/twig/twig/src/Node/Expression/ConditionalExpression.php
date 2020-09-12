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
namespace WPR\Vendor\Twig\Node\Expression;

use WPR\Vendor\Twig\Compiler;
class ConditionalExpression extends \WPR\Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\WPR\Vendor\Twig\Node\Expression\AbstractExpression $expr1, \WPR\Vendor\Twig\Node\Expression\AbstractExpression $expr2, \WPR\Vendor\Twig\Node\Expression\AbstractExpression $expr3, int $lineno)
    {
        parent::__construct(['expr1' => $expr1, 'expr2' => $expr2, 'expr3' => $expr3], [], $lineno);
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('((')->subcompile($this->getNode('expr1'))->raw(') ? (')->subcompile($this->getNode('expr2'))->raw(') : (')->subcompile($this->getNode('expr3'))->raw('))');
    }
}
