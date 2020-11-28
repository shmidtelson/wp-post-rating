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
namespace WPR_Vendor\Twig\Node\Expression\Unary;

use WPR_Vendor\Twig\Compiler;
use WPR_Vendor\Twig\Node\Expression\AbstractExpression;
use WPR_Vendor\Twig\Node\Node;
abstract class AbstractUnary extends \WPR_Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\WPR_Vendor\Twig\Node\Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw(' ');
        $this->operator($compiler);
        $compiler->subcompile($this->getNode('node'));
    }
    public abstract function operator(\WPR_Vendor\Twig\Compiler $compiler) : \WPR_Vendor\Twig\Compiler;
}
