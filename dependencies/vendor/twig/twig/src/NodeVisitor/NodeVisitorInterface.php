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
 * Interface for node visitor classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface NodeVisitorInterface
{
    /**
     * Called before child nodes are visited.
     *
     * @return Node The modified node
     */
    public function enterNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : \WPR\Vendor\Twig\Node\Node;
    /**
     * Called after child nodes are visited.
     *
     * @return Node|null The modified node or null if the node must be removed
     */
    public function leaveNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : ?\WPR\Vendor\Twig\Node\Node;
    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return int The priority level
     */
    public function getPriority();
}
