<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Node\Expression;

use WPR\Vendor\Twig\Compiler;
class TempNameExpression extends \WPR\Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(string $name, int $lineno)
    {
        parent::__construct([], ['name' => $name], $lineno);
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('$_')->raw($this->getAttribute('name'))->raw('_');
    }
}
