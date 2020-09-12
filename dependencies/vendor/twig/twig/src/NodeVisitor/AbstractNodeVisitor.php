<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\NodeVisitor;

use WPR\Vendor\Twig\Environment;
use WPR\Vendor\Twig\Node\Node;
/**
 * Used to make node visitors compatible with Twig 1.x and 2.x.
 *
 * To be removed in Twig 3.1.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractNodeVisitor implements \WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface
{
    public final function enterNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : \WPR\Vendor\Twig\Node\Node
    {
        return $this->doEnterNode($node, $env);
    }
    public final function leaveNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : ?\WPR\Vendor\Twig\Node\Node
    {
        return $this->doLeaveNode($node, $env);
    }
    /**
     * Called before child nodes are visited.
     *
     * @return Node The modified node
     */
    protected abstract function doEnterNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env);
    /**
     * Called after child nodes are visited.
     *
     * @return Node|null The modified node or null if the node must be removed
     */
    protected abstract function doLeaveNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env);
}
