<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\NodeVisitor;

use WPR_Vendor\Twig\Environment;
use WPR_Vendor\Twig\Node\Expression\BlockReferenceExpression;
use WPR_Vendor\Twig\Node\Expression\ConditionalExpression;
use WPR_Vendor\Twig\Node\Expression\ConstantExpression;
use WPR_Vendor\Twig\Node\Expression\FilterExpression;
use WPR_Vendor\Twig\Node\Expression\FunctionExpression;
use WPR_Vendor\Twig\Node\Expression\GetAttrExpression;
use WPR_Vendor\Twig\Node\Expression\MethodCallExpression;
use WPR_Vendor\Twig\Node\Expression\NameExpression;
use WPR_Vendor\Twig\Node\Expression\ParentExpression;
use WPR_Vendor\Twig\Node\Node;
final class SafeAnalysisNodeVisitor implements \WPR_Vendor\Twig\NodeVisitor\NodeVisitorInterface
{
    private $data = [];
    private $safeVars = [];
    public function setSafeVars(array $safeVars) : void
    {
        $this->safeVars = $safeVars;
    }
    public function getSafe(\WPR_Vendor\Twig\Node\Node $node)
    {
        $hash = \spl_object_hash($node);
        if (!isset($this->data[$hash])) {
            return;
        }
        foreach ($this->data[$hash] as $bucket) {
            if ($bucket['key'] !== $node) {
                continue;
            }
            if (\in_array('html_attr', $bucket['value'])) {
                $bucket['value'][] = 'html';
            }
            return $bucket['value'];
        }
    }
    private function setSafe(\WPR_Vendor\Twig\Node\Node $node, array $safe) : void
    {
        $hash = \spl_object_hash($node);
        if (isset($this->data[$hash])) {
            foreach ($this->data[$hash] as &$bucket) {
                if ($bucket['key'] === $node) {
                    $bucket['value'] = $safe;
                    return;
                }
            }
        }
        $this->data[$hash][] = ['key' => $node, 'value' => $safe];
    }
    public function enterNode(\WPR_Vendor\Twig\Node\Node $node, \WPR_Vendor\Twig\Environment $env) : \WPR_Vendor\Twig\Node\Node
    {
        return $node;
    }
    public function leaveNode(\WPR_Vendor\Twig\Node\Node $node, \WPR_Vendor\Twig\Environment $env) : ?\WPR_Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR_Vendor\Twig\Node\Expression\ConstantExpression) {
            // constants are marked safe for all
            $this->setSafe($node, ['all']);
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\Expression\BlockReferenceExpression) {
            // blocks are safe by definition
            $this->setSafe($node, ['all']);
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\Expression\ParentExpression) {
            // parent block is safe by definition
            $this->setSafe($node, ['all']);
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\Expression\ConditionalExpression) {
            // intersect safeness of both operands
            $safe = $this->intersectSafe($this->getSafe($node->getNode('expr2')), $this->getSafe($node->getNode('expr3')));
            $this->setSafe($node, $safe);
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\Expression\FilterExpression) {
            // filter expression is safe when the filter is safe
            $name = $node->getNode('filter')->getAttribute('value');
            $args = $node->getNode('arguments');
            if ($filter = $env->getFilter($name)) {
                $safe = $filter->getSafe($args);
                if (null === $safe) {
                    $safe = $this->intersectSafe($this->getSafe($node->getNode('node')), $filter->getPreservesSafety());
                }
                $this->setSafe($node, $safe);
            } else {
                $this->setSafe($node, []);
            }
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\Expression\FunctionExpression) {
            // function expression is safe when the function is safe
            $name = $node->getAttribute('name');
            $args = $node->getNode('arguments');
            if ($function = $env->getFunction($name)) {
                $this->setSafe($node, $function->getSafe($args));
            } else {
                $this->setSafe($node, []);
            }
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\Expression\MethodCallExpression) {
            if ($node->getAttribute('safe')) {
                $this->setSafe($node, ['all']);
            } else {
                $this->setSafe($node, []);
            }
        } elseif ($node instanceof \WPR_Vendor\Twig\Node\Expression\GetAttrExpression && $node->getNode('node') instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression) {
            $name = $node->getNode('node')->getAttribute('name');
            if (\in_array($name, $this->safeVars)) {
                $this->setSafe($node, ['all']);
            } else {
                $this->setSafe($node, []);
            }
        } else {
            $this->setSafe($node, []);
        }
        return $node;
    }
    private function intersectSafe(array $a = null, array $b = null) : array
    {
        if (null === $a || null === $b) {
            return [];
        }
        if (\in_array('all', $a)) {
            return $b;
        }
        if (\in_array('all', $b)) {
            return $a;
        }
        return \array_intersect($a, $b);
    }
    public function getPriority() : int
    {
        return 0;
    }
}
