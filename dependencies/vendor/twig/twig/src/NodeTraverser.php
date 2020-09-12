<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig;

use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface;
/**
 * A node traverser.
 *
 * It visits all nodes and their children and calls the given visitor for each.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class NodeTraverser
{
    private $env;
    private $visitors = [];
    /**
     * @param NodeVisitorInterface[] $visitors
     */
    public function __construct(\WPR\Vendor\Twig\Environment $env, array $visitors = [])
    {
        $this->env = $env;
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
    }
    public function addVisitor(\WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface $visitor) : void
    {
        $this->visitors[$visitor->getPriority()][] = $visitor;
    }
    /**
     * Traverses a node and calls the registered visitors.
     */
    public function traverse(\WPR\Vendor\Twig\Node\Node $node) : \WPR\Vendor\Twig\Node\Node
    {
        \ksort($this->visitors);
        foreach ($this->visitors as $visitors) {
            foreach ($visitors as $visitor) {
                $node = $this->traverseForVisitor($visitor, $node);
            }
        }
        return $node;
    }
    private function traverseForVisitor(\WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface $visitor, \WPR\Vendor\Twig\Node\Node $node) : ?\WPR\Vendor\Twig\Node\Node
    {
        $node = $visitor->enterNode($node, $this->env);
        foreach ($node as $k => $n) {
            if (null !== ($m = $this->traverseForVisitor($visitor, $n))) {
                if ($m !== $n) {
                    $node->setNode($k, $m);
                }
            } else {
                $node->removeNode($k);
            }
        }
        return $visitor->leaveNode($node, $this->env);
    }
}
