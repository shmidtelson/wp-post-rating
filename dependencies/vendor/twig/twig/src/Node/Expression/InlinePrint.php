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
use WPR\Vendor\Twig\Node\Node;
/**
 * @internal
 */
final class InlinePrint extends \WPR\Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\WPR\Vendor\Twig\Node\Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('print (')->subcompile($this->getNode('node'))->raw(')');
    }
}
