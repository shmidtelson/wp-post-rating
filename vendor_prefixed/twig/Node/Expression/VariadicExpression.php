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
class VariadicExpression extends \WPR_Vendor\Twig\Node\Expression\ArrayExpression
{
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('...');
        parent::compile($compiler);
    }
}
