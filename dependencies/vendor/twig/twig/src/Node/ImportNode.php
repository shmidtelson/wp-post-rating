<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Node;

use WPR\Vendor\Twig\Compiler;
use WPR\Vendor\Twig\Node\Expression\AbstractExpression;
use WPR\Vendor\Twig\Node\Expression\NameExpression;
/**
 * Represents an import node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ImportNode extends \WPR\Vendor\Twig\Node\Node
{
    public function __construct(\WPR\Vendor\Twig\Node\Expression\AbstractExpression $expr, \WPR\Vendor\Twig\Node\Expression\AbstractExpression $var, int $lineno, string $tag = null, bool $global = \true)
    {
        parent::__construct(['expr' => $expr, 'var' => $var], ['global' => $global], $lineno, $tag);
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write('$macros[')->repr($this->getNode('var')->getAttribute('name'))->raw('] = ');
        if ($this->getAttribute('global')) {
            $compiler->raw('$this->macros[')->repr($this->getNode('var')->getAttribute('name'))->raw('] = ');
        }
        if ($this->getNode('expr') instanceof \WPR\Vendor\Twig\Node\Expression\NameExpression && '_self' === $this->getNode('expr')->getAttribute('name')) {
            $compiler->raw('$this');
        } else {
            $compiler->raw('$this->loadTemplate(')->subcompile($this->getNode('expr'))->raw(', ')->repr($this->getTemplateName())->raw(', ')->repr($this->getTemplateLine())->raw(')->unwrap()');
        }
        $compiler->raw(";\n");
    }
}
