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
class PowerBinary extends \WPR_Vendor\Twig\Node\Expression\Binary\AbstractBinary
{
    public function operator(\WPR_Vendor\Twig\Compiler $compiler) : \WPR_Vendor\Twig\Compiler
    {
        return $compiler->raw('**');
    }
}
