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
use WPR_Vendor\Twig\Node\Node;
class TestExpression extends \WPR_Vendor\Twig\Node\Expression\CallExpression
{
    public function __construct(\WPR_Vendor\Twig\Node\Node $node, string $name, ?\WPR_Vendor\Twig\Node\Node $arguments, int $lineno)
    {
        $nodes = ['node' => $node];
        if (null !== $arguments) {
            $nodes['arguments'] = $arguments;
        }
        parent::__construct($nodes, ['name' => $name], $lineno);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $name = $this->getAttribute('name');
        $test = $compiler->getEnvironment()->getTest($name);
        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'test');
        $this->setAttribute('arguments', $test->getArguments());
        $this->setAttribute('callable', $test->getCallable());
        $this->setAttribute('is_variadic', $test->isVariadic());
        $this->compileCallable($compiler);
    }
}