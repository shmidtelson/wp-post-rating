<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Node\Expression\Test;

use WPR\Vendor\Twig\Compiler;
use WPR\Vendor\Twig\Error\SyntaxError;
use WPR\Vendor\Twig\Node\Expression\ArrayExpression;
use WPR\Vendor\Twig\Node\Expression\BlockReferenceExpression;
use WPR\Vendor\Twig\Node\Expression\ConstantExpression;
use WPR\Vendor\Twig\Node\Expression\FunctionExpression;
use WPR\Vendor\Twig\Node\Expression\GetAttrExpression;
use WPR\Vendor\Twig\Node\Expression\MethodCallExpression;
use WPR\Vendor\Twig\Node\Expression\NameExpression;
use WPR\Vendor\Twig\Node\Expression\TestExpression;
use WPR\Vendor\Twig\Node\Node;
/**
 * Checks if a variable is defined in the current context.
 *
 *    {# defined works with variable names and variable attributes #}
 *    {% if foo is defined %}
 *        {# ... #}
 *    {% endif %}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DefinedTest extends \WPR\Vendor\Twig\Node\Expression\TestExpression
{
    public function __construct(\WPR\Vendor\Twig\Node\Node $node, string $name, ?\WPR\Vendor\Twig\Node\Node $arguments, int $lineno)
    {
        if ($node instanceof \WPR\Vendor\Twig\Node\Expression\NameExpression) {
            $node->setAttribute('is_defined_test', \true);
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\Expression\GetAttrExpression) {
            $node->setAttribute('is_defined_test', \true);
            $this->changeIgnoreStrictCheck($node);
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\Expression\BlockReferenceExpression) {
            $node->setAttribute('is_defined_test', \true);
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\Expression\FunctionExpression && 'constant' === $node->getAttribute('name')) {
            $node->setAttribute('is_defined_test', \true);
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\Expression\ConstantExpression || $node instanceof \WPR\Vendor\Twig\Node\Expression\ArrayExpression) {
            $node = new \WPR\Vendor\Twig\Node\Expression\ConstantExpression(\true, $node->getTemplateLine());
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\Expression\MethodCallExpression) {
            $node->setAttribute('is_defined_test', \true);
        } else {
            throw new \WPR\Vendor\Twig\Error\SyntaxError('The "defined" test only works with simple variables.', $lineno);
        }
        parent::__construct($node, $name, $arguments, $lineno);
    }
    private function changeIgnoreStrictCheck(\WPR\Vendor\Twig\Node\Expression\GetAttrExpression $node)
    {
        $node->setAttribute('optimizable', \false);
        $node->setAttribute('ignore_strict_check', \true);
        if ($node->getNode('node') instanceof \WPR\Vendor\Twig\Node\Expression\GetAttrExpression) {
            $this->changeIgnoreStrictCheck($node->getNode('node'));
        }
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
