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
use WPR\Vendor\Twig\Node\Node;
class FilterExpression extends \WPR\Vendor\Twig\Node\Expression\CallExpression
{
    public function __construct(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Node\Expression\ConstantExpression $filterName, \WPR\Vendor\Twig\Node\Node $arguments, int $lineno, string $tag = null)
    {
        parent::__construct(['node' => $node, 'filter' => $filterName, 'arguments' => $arguments], [], $lineno, $tag);
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $name = $this->getNode('filter')->getAttribute('value');
        $filter = $compiler->getEnvironment()->getFilter($name);
        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'filter');
        $this->setAttribute('needs_environment', $filter->needsEnvironment());
        $this->setAttribute('needs_context', $filter->needsContext());
        $this->setAttribute('arguments', $filter->getArguments());
        $this->setAttribute('callable', $filter->getCallable());
        $this->setAttribute('is_variadic', $filter->isVariadic());
        $this->compileCallable($compiler);
    }
}
