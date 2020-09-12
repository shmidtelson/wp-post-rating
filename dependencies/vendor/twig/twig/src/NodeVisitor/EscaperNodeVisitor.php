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
use WPR\Vendor\Twig\Extension\EscaperExtension;
use WPR\Vendor\Twig\Node\AutoEscapeNode;
use WPR\Vendor\Twig\Node\BlockNode;
use WPR\Vendor\Twig\Node\BlockReferenceNode;
use WPR\Vendor\Twig\Node\DoNode;
use WPR\Vendor\Twig\Node\Expression\ConditionalExpression;
use WPR\Vendor\Twig\Node\Expression\ConstantExpression;
use WPR\Vendor\Twig\Node\Expression\FilterExpression;
use WPR\Vendor\Twig\Node\Expression\InlinePrint;
use WPR\Vendor\Twig\Node\ImportNode;
use WPR\Vendor\Twig\Node\ModuleNode;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Node\PrintNode;
use WPR\Vendor\Twig\NodeTraverser;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class EscaperNodeVisitor implements \WPR\Vendor\Twig\NodeVisitor\NodeVisitorInterface
{
    private $statusStack = [];
    private $blocks = [];
    private $safeAnalysis;
    private $traverser;
    private $defaultStrategy = \false;
    private $safeVars = [];
    public function __construct()
    {
        $this->safeAnalysis = new \WPR\Vendor\Twig\NodeVisitor\SafeAnalysisNodeVisitor();
    }
    public function enterNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : \WPR\Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR\Vendor\Twig\Node\ModuleNode) {
            if ($env->hasExtension(\WPR\Vendor\Twig\Extension\EscaperExtension::class) && ($defaultStrategy = $env->getExtension(\WPR\Vendor\Twig\Extension\EscaperExtension::class)->getDefaultStrategy($node->getTemplateName()))) {
                $this->defaultStrategy = $defaultStrategy;
            }
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\AutoEscapeNode) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\BlockNode) {
            $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ? $this->blocks[$node->getAttribute('name')] : $this->needEscaping($env);
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\ImportNode) {
            $this->safeVars[] = $node->getNode('var')->getAttribute('name');
        }
        return $node;
    }
    public function leaveNode(\WPR\Vendor\Twig\Node\Node $node, \WPR\Vendor\Twig\Environment $env) : ?\WPR\Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR\Vendor\Twig\Node\ModuleNode) {
            $this->defaultStrategy = \false;
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\Expression\FilterExpression) {
            return $this->preEscapeFilterNode($node, $env);
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\PrintNode && \false !== ($type = $this->needEscaping($env))) {
            $expression = $node->getNode('expr');
            if ($expression instanceof \WPR\Vendor\Twig\Node\Expression\ConditionalExpression && $this->shouldUnwrapConditional($expression, $env, $type)) {
                return new \WPR\Vendor\Twig\Node\DoNode($this->unwrapConditional($expression, $env, $type), $expression->getTemplateLine());
            }
            return $this->escapePrintNode($node, $env, $type);
        }
        if ($node instanceof \WPR\Vendor\Twig\Node\AutoEscapeNode || $node instanceof \WPR\Vendor\Twig\Node\BlockNode) {
            \array_pop($this->statusStack);
        } elseif ($node instanceof \WPR\Vendor\Twig\Node\BlockReferenceNode) {
            $this->blocks[$node->getAttribute('name')] = $this->needEscaping($env);
        }
        return $node;
    }
    private function shouldUnwrapConditional(\WPR\Vendor\Twig\Node\Expression\ConditionalExpression $expression, \WPR\Vendor\Twig\Environment $env, string $type) : bool
    {
        $expr2Safe = $this->isSafeFor($type, $expression->getNode('expr2'), $env);
        $expr3Safe = $this->isSafeFor($type, $expression->getNode('expr3'), $env);
        return $expr2Safe !== $expr3Safe;
    }
    private function unwrapConditional(\WPR\Vendor\Twig\Node\Expression\ConditionalExpression $expression, \WPR\Vendor\Twig\Environment $env, string $type) : \WPR\Vendor\Twig\Node\Expression\ConditionalExpression
    {
        // convert "echo a ? b : c" to "a ? echo b : echo c" recursively
        $expr2 = $expression->getNode('expr2');
        if ($expr2 instanceof \WPR\Vendor\Twig\Node\Expression\ConditionalExpression && $this->shouldUnwrapConditional($expr2, $env, $type)) {
            $expr2 = $this->unwrapConditional($expr2, $env, $type);
        } else {
            $expr2 = $this->escapeInlinePrintNode(new \WPR\Vendor\Twig\Node\Expression\InlinePrint($expr2, $expr2->getTemplateLine()), $env, $type);
        }
        $expr3 = $expression->getNode('expr3');
        if ($expr3 instanceof \WPR\Vendor\Twig\Node\Expression\ConditionalExpression && $this->shouldUnwrapConditional($expr3, $env, $type)) {
            $expr3 = $this->unwrapConditional($expr3, $env, $type);
        } else {
            $expr3 = $this->escapeInlinePrintNode(new \WPR\Vendor\Twig\Node\Expression\InlinePrint($expr3, $expr3->getTemplateLine()), $env, $type);
        }
        return new \WPR\Vendor\Twig\Node\Expression\ConditionalExpression($expression->getNode('expr1'), $expr2, $expr3, $expression->getTemplateLine());
    }
    private function escapeInlinePrintNode(\WPR\Vendor\Twig\Node\Expression\InlinePrint $node, \WPR\Vendor\Twig\Environment $env, string $type) : \WPR\Vendor\Twig\Node\Node
    {
        $expression = $node->getNode('node');
        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }
        return new \WPR\Vendor\Twig\Node\Expression\InlinePrint($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
    }
    private function escapePrintNode(\WPR\Vendor\Twig\Node\PrintNode $node, \WPR\Vendor\Twig\Environment $env, string $type) : \WPR\Vendor\Twig\Node\Node
    {
        if (\false === $type) {
            return $node;
        }
        $expression = $node->getNode('expr');
        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }
        $class = \get_class($node);
        return new $class($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
    }
    private function preEscapeFilterNode(\WPR\Vendor\Twig\Node\Expression\FilterExpression $filter, \WPR\Vendor\Twig\Environment $env) : \WPR\Vendor\Twig\Node\Expression\FilterExpression
    {
        $name = $filter->getNode('filter')->getAttribute('value');
        $type = $env->getFilter($name)->getPreEscape();
        if (null === $type) {
            return $filter;
        }
        $node = $filter->getNode('node');
        if ($this->isSafeFor($type, $node, $env)) {
            return $filter;
        }
        $filter->setNode('node', $this->getEscaperFilter($type, $node));
        return $filter;
    }
    private function isSafeFor(string $type, \WPR\Vendor\Twig\Node\Node $expression, \WPR\Vendor\Twig\Environment $env) : bool
    {
        $safe = $this->safeAnalysis->getSafe($expression);
        if (null === $safe) {
            if (null === $this->traverser) {
                $this->traverser = new \WPR\Vendor\Twig\NodeTraverser($env, [$this->safeAnalysis]);
            }
            $this->safeAnalysis->setSafeVars($this->safeVars);
            $this->traverser->traverse($expression);
            $safe = $this->safeAnalysis->getSafe($expression);
        }
        return \in_array($type, $safe) || \in_array('all', $safe);
    }
    private function needEscaping(\WPR\Vendor\Twig\Environment $env)
    {
        if (\count($this->statusStack)) {
            return $this->statusStack[\count($this->statusStack) - 1];
        }
        return $this->defaultStrategy ? $this->defaultStrategy : \false;
    }
    private function getEscaperFilter(string $type, \WPR\Vendor\Twig\Node\Node $node) : \WPR\Vendor\Twig\Node\Expression\FilterExpression
    {
        $line = $node->getTemplateLine();
        $name = new \WPR\Vendor\Twig\Node\Expression\ConstantExpression('escape', $line);
        $args = new \WPR\Vendor\Twig\Node\Node([new \WPR\Vendor\Twig\Node\Expression\ConstantExpression((string) $type, $line), new \WPR\Vendor\Twig\Node\Expression\ConstantExpression(null, $line), new \WPR\Vendor\Twig\Node\Expression\ConstantExpression(\true, $line)]);
        return new \WPR\Vendor\Twig\Node\Expression\FilterExpression($node, $name, $args, $line);
    }
    public function getPriority() : int
    {
        return 0;
    }
}
