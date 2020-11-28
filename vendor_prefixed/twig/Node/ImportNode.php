<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Node;

use WPR_Vendor\Twig\Compiler;
use WPR_Vendor\Twig\Node\Expression\AbstractExpression;
use WPR_Vendor\Twig\Node\Expression\NameExpression;
/**
 * Represents an import node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ImportNode extends \WPR_Vendor\Twig\Node\Node
{
    public function __construct(\WPR_Vendor\Twig\Node\Expression\AbstractExpression $expr, \WPR_Vendor\Twig\Node\Expression\AbstractExpression $var, int $lineno, string $tag = null, bool $global = \true)
    {
        parent::__construct(['expr' => $expr, 'var' => $var], ['global' => $global], $lineno, $tag);
    }
    public function compile(\WPR_Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write('$macros[')->repr($this->getNode('var')->getAttribute('name'))->raw('] = ');
        if ($this->getAttribute('global')) {
            $compiler->raw('$this->macros[')->repr($this->getNode('var')->getAttribute('name'))->raw('] = ');
        }
        if ($this->getNode('expr') instanceof \WPR_Vendor\Twig\Node\Expression\NameExpression && '_self' === $this->getNode('expr')->getAttribute('name')) {
            $compiler->raw('$this');
        } else {
            $compiler->raw('$this->loadTemplate(')->subcompile($this->getNode('expr'))->raw(', ')->repr($this->getTemplateName())->raw(', ')->repr($this->getTemplateLine())->raw(')->unwrap()');
        }
        $compiler->raw(";\n");
    }
}
