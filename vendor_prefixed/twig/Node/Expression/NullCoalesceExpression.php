<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Node\Expression;

use WPR_Vendor\Twig\Compiler;
use WPR_Vendor\Twig\Node\Expression\Binary\AndBinary;
use WPR_Vendor\Twig\Node\Expression\Test\DefinedTest;
use WPR_Vendor\Twig\Node\Expression\Test\NullTest;
use WPR_Vendor\Twig\Node\Expression\Unary\NotUnary;
use WPR_Vendor\Twig\Node\Node;
class NullCoalesceExpression extends \WPR_Vendor\Twig\Node\Expression\ConditionalExpression
{
    public function __construct(\WPR_Vendor\Twig\Node\Node $left, \WPR_Vendor\Twig\Node\Node $right, int $lineno)
    {
        $test = new \WPR_Vendor\Twig\Node\Expression\Test\DefinedTest(clone $left, 'defined', new \WPR_Vendor\Twig\Node\Node(), $left->getTemplateLine());
        // for "block()", we don't need the null test as the return value is always a string
        if (!$left instanceof \WPR_Vendor\Twig\Node\Expression\BlockReferenceExpression) {
            $test = new \WPR_Vendor\Twig\Node\Expression\Binary\AndBinary($test, new \WPR_Vendor\Twig\Node\Expression\Unary\NotUnary(new \WPR_Vendor\Twig\Node\Expression\Test\NullTest($left, 'null', new \WPR_Vendor\Twig\Node\Node(), $left->getTemplateLine()), $left->getTemplateLine()), $left->getTemplateLine());
        }
        parent::__construct($test, $left, $right, $lineno);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        /*
         * This optimizes only one case. PHP 7 also supports more complex expressions
         * that can return null. So, for instance, if log is defined, log("foo") ?? "..." works,
         * but log($a["foo"]) ?? "..." does not if $a["foo"] is not defined. More advanced
         * cases might be implemented as an optimizer node visitor, but has not been done
         * as benefits are probably not worth the added complexity.
         */
        if ($this->getNode('expr2') instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression) {
            $this->getNode('expr2')->setAttribute('always_defined', \true);
            $compiler->raw('((')->subcompile($this->getNode('expr2'))->raw(') ?? (')->subcompile($this->getNode('expr3'))->raw('))');
        } else {
            parent::compile($compiler);
        }
    }
}
