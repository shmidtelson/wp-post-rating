<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Profiler\Node;

use WPR\Vendor\Twig\Compiler;
use WPR\Vendor\Twig\Node\Node;
/**
 * Represents a profile enter node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class EnterProfileNode extends \WPR\Vendor\Twig\Node\Node
{
    public function __construct(string $extensionName, string $type, string $name, string $varName)
    {
        parent::__construct([], ['extension_name' => $extensionName, 'name' => $name, 'type' => $type, 'var_name' => $varName]);
    }
    public function compile(\WPR\Vendor\Twig\Compiler $compiler) : void
    {
        $compiler->write(\sprintf('$%s = $this->extensions[', $this->getAttribute('var_name')))->repr($this->getAttribute('extension_name'))->raw("];\n")->write(\sprintf('$%s->enter($%s = new \\Twig\\Profiler\\Profile($this->getTemplateName(), ', $this->getAttribute('var_name'), $this->getAttribute('var_name') . '_prof'))->repr($this->getAttribute('type'))->raw(', ')->repr($this->getAttribute('name'))->raw("));\n\n");
    }
}