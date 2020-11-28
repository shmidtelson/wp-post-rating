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
use WPR_Vendor\Twig\Node\Expression\AssignNameExpression;
use WPR_Vendor\Twig\Node\Expression\ConstantExpression;
use WPR_Vendor\Twig\Node\Expression\GetAttrExpression;
use WPR_Vendor\Twig\Node\Expression\MethodCallExpression;
use WPR_Vendor\Twig\Node\Expression\NameExpression;
use WPR_Vendor\Twig\Node\ImportNode;
use WPR_Vendor\Twig\Node\ModuleNode;
use WPR_Vendor\Twig\Node\Node;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class MacroAutoImportNodeVisitor implements \WPR_Vendor\Twig\NodeVisitor\NodeVisitorInterface
{
    private $inAModule = \false;
    private $hasMacroCalls = \false;
    public function enterNode(\WPR_Vendor\Twig\Node\Node $node, \WPR_Vendor\Twig\Environment $env) : \WPR_Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR_Vendor\Twig\Node\ModuleNode) {
            $this->inAModule = \true;
            $this->hasMacroCalls = \false;
        }
        return $node;
    }
    public function leaveNode(\WPR_Vendor\Twig\Node\Node $node, \WPR_Vendor\Twig\Environment $env) : \WPR_Vendor\Twig\Node\Node
    {
        if ($node instanceof \WPR_Vendor\Twig\Node\ModuleNode) {
            $this->inAModule = \false;
            if ($this->hasMacroCalls) {
                $node->getNode('constructor_end')->setNode('_auto_macro_import', new \WPR_Vendor\Twig\Node\ImportNode(new \WPR_Vendor\Twig\Node\Expression\NameExpression('_self', 0), new \WPR_Vendor\Twig\Node\Expression\AssignNameExpression('_self', 0), 0, 'import', \true));
            }
        } elseif ($this->inAModule) {
            if ($node instanceof \WPR_Vendor\Twig\Node\Expression\GetAttrExpression && $node->getNode('node') instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression && '_self' === $node->getNode('node')->getAttribute('name') && $node->getNode('attribute') instanceof \WPR_Vendor\Twig\Node\Expression\ConstantExpression) {
                $this->hasMacroCalls = \true;
                $name = $node->getNode('attribute')->getAttribute('value');
                $node = new \WPR_Vendor\Twig\Node\Expression\MethodCallExpression($node->getNode('node'), 'macro_' . $name, $node->getNode('arguments'), $node->getTemplateLine());
                $node->setAttribute('safe', \true);
            }
        }
        return $node;
    }
    public function getPriority() : int
    {
        // we must be ran before auto-escaping
        return -10;
    }
}
