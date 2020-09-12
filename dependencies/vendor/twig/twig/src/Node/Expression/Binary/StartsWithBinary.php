<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Node\Expression\Binary;

use WPR\Vendor\Twig\Compiler;
class StartsWithBinary extends \WPR\Vendor\Twig\Node\Expression\Binary\AbstractBinary
{
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $left = $compiler->getVarName();
        $right = $compiler->getVarName();
        $compiler->raw(\sprintf('(is_string($%s = ', $left))->subcompile($this->getNode('left'))->raw(\sprintf(') && is_string($%s = ', $right))->subcompile($this->getNode('right'))->raw(\sprintf(') && (\'\' === $%2$s || 0 === strpos($%1$s, $%2$s)))', $left, $right));
    }
    public function operator(\WPR\Vendor\Twig\Compiler $compiler) : \WPR\Vendor\Twig\Compiler
    {
        return $compiler->raw('');
    }
}
