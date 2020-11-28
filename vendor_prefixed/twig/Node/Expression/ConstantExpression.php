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
namespace WPR_Vendor\Twig\Node\Expression;

use WPR_Vendor\Twig\Compiler;
class ConstantExpression extends \WPR_Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct($value, int $lineno)
    {
        parent::__construct([], ['value' => $value], $lineno);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->repr($this->getAttribute('value'));
    }
}
