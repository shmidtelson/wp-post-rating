<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Profiler\NodeVisitor;

use WPR_Vendor\Twig\Environment;
use WPR_Vendor\Twig\Node\BlockNode;
use WPR_Vendor\Twig\Node\BodyNode;
use WPR_Vendor\Twig\Node\MacroNode;
use WPR_Vendor\Twig\Node\ModuleNode;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\NodeVisitor\NodeVisitorInterface;
use WPR_Vendor\Twig\Profiler\Node\EnterProfileNode;
use WPR_Vendor\Twig\Profiler\Node\LeaveProfileNode;
use WPR_Vendor\Twig\Profiler\Profile;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ProfilerNodeVisitor implements \WPR_Vendor\Twig\NodeVisitor\NodeVisitorInterface
{
    private $extensionName;
    public function __construct(string $extensionName)
    {
        $this->extensionName = $extensionName;
    }
    public function enterNode(\WPR_Vendor\Twig\Node\Node $node, \WPR_Vendor\Twig\Environment $env) : \WPR_Vendor\Twig\Node\Node
    {
        return $node;
    }
    public function leaveNode(\WPR_Vendor\Twig\Node\Node $node, \WPR_Vendor\Twig\Environment $env) : ?\WPR_Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR_Vendor\Twig\Node\ModuleNode) {
            $varName = $this->getVarName();
            $node->setNode('display_start', new \WPR_Vendor\Twig\Node\Node([new \WPR_Vendor\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \WPR_Vendor\Twig\Profiler\Profile::TEMPLATE, $node->getTemplateName(), $varName), $node->getNode('display_start')]));
            $node->setNode('display_end', new \WPR_Vendor\Twig\Node\Node([new \WPR_Vendor\Twig\Profiler\Node\LeaveProfileNode($varName), $node->getNode('display_end')]));
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\BlockNode) {
            $varName = $this->getVarName();
            $node->setNode('body', new \WPR_Vendor\Twig\Node\BodyNode([new \WPR_Vendor\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \WPR_Vendor\Twig\Profiler\Profile::BLOCK, $node->getAttribute('name'), $varName), $node->getNode('body'), new \WPR_Vendor\Twig\Profiler\Node\LeaveProfileNode($varName)]));
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\MacroNode) {
            $varName = $this->getVarName();
            $node->setNode('body', new \WPR_Vendor\Twig\Node\BodyNode([new \WPR_Vendor\Twig\Profiler\Node\EnterProfileNode($this->extensionName, \WPR_Vendor\Twig\Profiler\Profile::MACRO, $node->getAttribute('name'), $varName), $node->getNode('body'), new \WPR_Vendor\Twig\Profiler\Node\LeaveProfileNode($varName)]));
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
