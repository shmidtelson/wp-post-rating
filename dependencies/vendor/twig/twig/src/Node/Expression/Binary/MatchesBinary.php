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
class MatchesBinary extends \WPR\Vendor\Twig\Node\Expression\Binary\AbstractBinary
{
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('preg_match(')->subcompile($this->getNode('right'))->raw(', ')->subcompile($this->getNode('left'))->raw(')');
    }
    public function operator(\WPR\Vendor\Twig\Compiler $compiler) : \WPR\Vendor\Twig\Compiler
    {
        return $compiler->raw('');
    }
}
