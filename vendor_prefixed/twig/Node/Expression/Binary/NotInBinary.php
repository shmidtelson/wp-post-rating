<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Node\Expression\Binary;

use WPR_Vendor\Twig\Compiler;
class NotInBinary extends \WPR_Vendor\Twig\Node\Expression\Binary\AbstractBinary
{
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('!twig_in_filter(')->subcompile($this->getNode('left'))->raw(', ')->subcompile($this->getNode('right'))->raw(')');
    }
    public function operator(\WPR_Vendor\Twig\Compiler $compiler) : \WPR_Vendor\Twig\Compiler
    {
        return $compiler->raw('not in');
    }
}
