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
use WPR_Vendor\Twig\Node\Node;
/**
 * @internal
 */
final class InlinePrint extends \WPR_Vendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\WPR_Vendor\Twig\Node\Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->raw('print (')->subcompile($this->getNode('node'))->raw(')');
    }
}
