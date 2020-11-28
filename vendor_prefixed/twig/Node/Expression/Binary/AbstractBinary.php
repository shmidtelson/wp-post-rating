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
namespace WPR_Vendor\Twig\Node\Expression\Binary;

use WPR_Vendor\Twig\Compiler;
use WPR_Vendor\Twig\Node\Expression\AbstractExpression;
use WPR_Vendor\Twig\Node\Node;
abstract class AbstractBinary extends \WPR_Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\WPR_Vendor\Twig\Node\Node $left, \WPR_Vendor\Twig\Node\Node $right, int $lineno)
    {
        parent::__construct(['left' => $left, 'right' => $right], [], $lineno);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('(')->subcompile($this->getNode('left'))->raw(' ');
        $this->operator($compiler);
        $compiler->raw(' ')->subcompile($this->getNode('right'))->raw(')');
    }
    public abstract function operator(\WPR_Vendor\Twig\Compiler $compiler) : \WPR_Vendor\Twig\Compiler;
}
