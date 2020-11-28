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
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CheckSecurityCallNode extends \WPR_Vendor\Twig\Node\Node
{
    public function compile(\WPR_Vendor\Twig\Compiler $compiler)
    {
        $compiler->write("\$this->sandbox = \$this->env->getExtension('\\WPR_Vendor\\Twig\\Extension\\SandboxExtension');\n")->write("\$this->checkSecurity();\n");
    }
}
