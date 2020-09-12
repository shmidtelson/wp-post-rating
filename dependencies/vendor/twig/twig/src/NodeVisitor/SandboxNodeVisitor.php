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
use WPR\Vendor\Twig\Node\CheckSecurityNode;
use WPR\Vendor\Twig\Node\CheckToStringNode;
use WPR\Vendor\Twig\Node\Expression\Binary\ConcatBinary;
use WPR\Vendor\Twig\Node\Expression\Binary\RangeBinary;
use WPR\Vendor\Twig\Node\Expression\FilterExpression;
use WPR\Vendor\Twig\Node\Expression\FunctionExpression;
use WPR\Vendor\Twig\Node\Expression\GetAttrExpression;
use WPR\Vendor\Twig\Node\Expression\NameExpression;
use WPR\Vendor\Twig\Node\ModuleNode;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Node\PrintNode;
use WPR\Vendor\Twig\Node\SetNode;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class SandboxNodeVisitor implements \WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface
{
    private $inAModule = \false;
    private $tags;
    private $filters;
    private $functions;
    private $needsToStringWrap = \false;
    public function enterNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : \WPR\Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR\Vendor\Twig\Node\ModuleNode) {
            $this->inAModule = \true;
            $this->tags = [];
            $this->filters = [];
            $this->functions = [];
            return $node;
        } elseif ($this->inAModule) {
            // look for tags
            if ($node->getNodeTag() && !isset($this->tags[$node->getNodeTag()])) {
                $this->tags[$node->getNodeTag()] = $node;
            }
            // look for filters
            if ($node instanceof \WPR\Vendor\Twig\Node\Expression\FilterExpression && !isset($this->filters[$node->getNode('filter')->getAttribute('value')])) {
                $this->filters[$node->getNode('filter')->getAttribute('value')] = $node;
            }
            // look for functions
            if ($node instanceof \WPR\Vendor\Twig\Node\Expression\FunctionExpression && !isset($this->functions[$node->getAttribute('name')])) {
                $this->functions[$node->getAttribute('name')] = $node;
            }
            // the .. operator is equivalent to the range() function
            if ($node instanceof \WPR\Vendor\Twig\Node\Expression\Binary\RangeBinary && !isset($this->functions['range'])) {
                $this->functions['range'] = $node;
            }
            if ($node instanceof \WPR\Vendor\Twig\Node\PrintNode) {
                $this->needsToStringWrap = \true;
                $this->wrapNode($node, 'expr');
            }
            if ($node instanceof \WPR\Vendor\Twig\Node\SetNode && !$node->getAttribute('capture')) {
                $this->needsToStringWrap = \true;
            }
            // wrap outer nodes that can implicitly call __toString()
            if ($this->needsToStringWrap) {
                if ($node instanceof \WPR\Vendor\Twig\Node\Expression\Binary\ConcatBinary) {
                    $this->wrapNode($node, 'left');
                    $this->wrapNode($node, 'right');
                }
                if ($node instanceof \WPR\Vendor\Twig\Node\Expression\FilterExpression) {
                    $this->wrapNode($node, 'node');
                    $this->wrapArrayNode($node, 'arguments');
                }
                if ($node instanceof \WPR\Vendor\Twig\Node\Expression\FunctionExpression) {
                    $this->wrapArrayNode($node, 'arguments');
                }
            }
        }
        return $node;
    }
    public function leaveNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : ?\WPR\Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR\Vendor\Twig\Node\ModuleNode) {
            $this->inAModule = \false;
            $node->getNode('constructor_end')->setNode('_security_check', new \WPR\Vendor\Twig\Node\Node([new \WPR\Vendor\Twig\Node\CheckSecurityNode($this->filters, $this->tags, $this->functions), $node->getNode('display_start')]));
        } elseif ($this->inAModule) {
            if ($node instanceof \WPR\Vendor\Twig\Node\PrintNode || $node instanceof \WPR\Vendor\Twig\Node\SetNode) {
                $this->needsToStringWrap = \false;
            }
        }
        return $node;
    }
    private function wrapNode(\WPR\Vendor\Twig\Node\Node $node, string $name) : void
    {
        $expr = $node->getNode($name);
        if ($expr instanceof \WPR\Vendor\Twig\Node\Expression\NameExpression || $expr instanceof \WPR\Vendor\Twig\Node\Expression\GetAttrExpression) {
            $node->setNode($name, new \WPR\Vendor\Twig\Node\CheckToStringNode($expr));
        }
    }
    private function wrapArrayNode(\WPR\Vendor\Twig\Node\Node $node, string $name) : void
    {
        $args = $node->getNode($name);
        foreach ($args as $name => $_) {
            $this->wrapNode($args, $name);
        }
    }
    public function getPriority() : int
    {
        return 0;
    }
}
