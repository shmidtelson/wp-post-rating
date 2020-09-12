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
namespace WPR\Vendor\Twig\Node\Expression\Unary;

use WPR\Vendor\Twig\Compiler;
use WPR\Vendor\Twig\Node\Expression\AbstractExpression;
use WPR\Vendor\Twig\Node\Node;
abstract class AbstractUnary extends \WPR\Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\WPR\Vendor\Twig\Node\Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw(' ');
        $this->operator($compiler);
        $compiler->subcompile($this->getNode('node'));
    }
    public abstract function operator(\WPR\Vendor\Twig\Compiler $compiler) : \WPR\Vendor\Twig\Compiler;
}
