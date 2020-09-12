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
class NotUnary extends \WPR\Vendor\Twig\Node\Expression\Unary\AbstractUnary
{
    public function operator(\WPR\Vendor\Twig\Compiler $compiler) : \WPR\Vendor\Twig\Compiler
    {
        return $compiler->raw('!');
    }
}
