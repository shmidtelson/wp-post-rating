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
class LessBinary extends \WPR_Vendor\Twig\Node\Expression\Binary\AbstractBinary
{
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        if (\PHP_VERSION_ID >= 80000) {
            parent::compile($compiler);
            return;
        }
        $compiler->raw('(-1 === twig_compare(')->subcompile($this->getNode('left'))->raw(', ')->subcompile($this->getNode('right'))->raw('))');
    }
    public function operator(\WPR_Vendor\Twig\Compiler $compiler) : \WPR_Vendor\Twig\Compiler
    {
        return $compiler->raw('<');
    }
}
