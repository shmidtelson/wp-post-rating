<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Profiler\NodeVisitor;

use WPR\Vendor\Twig\Environment;
use WPR\Vendor\Twig\Node\BlockNode;
use WPR\Vendor\Twig\Node\BodyNode;
use WPR\Vendor\Twig\Node\MacroNode;
use WPR\Vendor\Twig\Node\ModuleNode;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface;
use WPR\Vendor\Twig\Profiler\Node\EnterProfileNode;
use WPR\Vendor\Twig\Profiler\Node\LeaveProfileNode;
use WPR\Vendor\Twig\Profiler\Profile;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ProfilerNodeVisitor implements \WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface
{
    private $extensionName;
    public function __construct(string $extensionName)
    {
        $this->extensionName = $extensionName;
    }
    public function enterNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : \WPR\Vendor\Twig\Node\Node
    {
        return $node;
    }
    public function leaveNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : ?\WPR\Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR\Vendor\Twig\Node\ModuleNode) {
            $varName = $this->getVarName();
            $node->setNode('display_start', new \WPR\Vendor\Twig\Node\Node([new \WPR\Vendor\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \WPR\Vendor\Twig\Profiler\Profile::TEMPLATE, $node->getTemplateName(), $varName), $node->getNode('display_start')]));
            $node->setNode('display_end', new \WPR\Vendor\Twig\Node\Node([new \WPR\Vendor\Twig\Profiler\Node\LeaveProfileNode($varName), $node->getNode('display_end')]));
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\BlockNode) {
            $varName = $this->getVarName();
            $node->setNode('body', new \WPR\Vendor\Twig\Node\BodyNode([new \WPR\Vendor\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \WPR\Vendor\Twig\Profiler\Profile::BLOCK, $node->getAttribute('name'), $varName), $node->getNode('body'), new \WPR\Vendor\Twig\Profiler\Node\LeaveProfileNode($varName)]));
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\MacroNode) {
            $varName = $this->getVarName();
            $node->setNode('body', new \WPR\Vendor\Twig\Node\BodyNode([new \WPR\Vendor\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \WPR\Vendor\Twig\Profiler\Profile::MACRO, $node->getAttribute('name'), $varName), $node->getNode('body'), new \WPR\Vendor\Twig\Profiler\Node\LeaveProfileNode($varName)]));
        }
        return $node;
    }
    private function getVarName() : string
    {
        return \sprintf('__internal_%s', \hash('sha256', $this->extensionName));
    }
    public function getPriority() : int
    {
        return 0;
    }
}
