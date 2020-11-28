<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Node\Expression\Filter;

use WPR_Vendor\Twig\Compiler;
use WPR_Vendor\Twig\Node\Expression\ConditionalExpression;
use WPR_Vendor\Twig\Node\Expression\ConstantExpression;
use WPR_Vendor\Twig\Node\Expression\FilterExpression;
use WPR_Vendor\Twig\Node\Expression\GetAttrExpression;
use WPR_Vendor\Twig\Node\Expression\NameExpression;
use WPR_Vendor\Twig\Node\Expression\Test\DefinedTest;
use WPR_Vendor\Twig\Node\Node;
/**
 * Returns the value or the default value when it is undefined or empty.
 *
 *  {{ var.foo|default('foo item on var is not defined') }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DefaultFilter extends \WPR_Vendor\Twig\Node\Expression\FilterExpression
{
    public function __construct(\WPR_Vendor\Twig\Node\Node $node, \WPR_Vendor\Twig\Node\Expression\ConstantExpression $filterName, \WPR_Vendor\Twig\Node\Node $arguments, int $lineno, string $tag = null)
    {
        $default = new \WPR_Vendor\Twig\Node\Expression\FilterExpression($node, new \WPR_Vendor\Twig\Node\Expression\ConstantExpression('default', $node->getTemplateLine()), $arguments, $node->getTemplateLine());
        if ('default' === $filterName->getAttribute('value') && ($node instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression || $node instanceof \WPR_Vendor\Twig\Node\Expression\GetAttrExpression)) {
            $test = new \WPR_Vendor\Twig\Node\Expression\Test\DefinedTest(clone $node, 'defined', new \WPR_Vendor\Twig\Node\Node(), $node->getTemplateLine());
            $false = \count($arguments) ? $arguments->getNode(0) : new \WPR_Vendor\Twig\Node\Expression\ConstantExpression('', $node->getTemplateLine());
            $node = new \WPR_Vendor\Twig\Node\Expression\ConditionalExpression($test, $default, $false, $node->getTemplateLine());
        } else {
            $node = $default;
        }
        parent::__construct($node, $filterName, $arguments, $lineno, $tag);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}