<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Extension;

use WPR\Vendor\Twig\NodeVisitor\SandboxNodeVisitor;
use WPR\Vendor\Twig\Sandbox\SecurityNotAllowedMethodError;
use WPR\Vendor\Twig\Sandbox\SecurityNotAllowedPropertyError;
use WPR\Vendor\Twig\Sandbox\SecurityPolicyInterface;
use WPR\Vendor\Twig\Source;
use WPR\Vendor\Twig\TokenParser\SandboxTokenParser;
final class SandboxExtension extends \WPR\Vendor\Twig\Extension\AbstractExtension
{
    private $sandboxedGlobally;
    private $sandboxed;
    private $policy;
    public function __construct(\WPR\Vendor\Twig\Sandbox\SecurityPolicyInterface $policy, $sandboxed = \false)
    {
        $this->policy = $policy;
        $this->sandboxedGlobally = $sandboxed;
    }
    public function getTokenParsers() : array
    {
        return [new \WPR\Vendor\Twig\TokenParser\SandboxTokenParser()];
    }
    public function getNodeVisitors() : array
    {
        return [new \WPR\Vendor\Twig\NodeVisitor\SandboxNodeVisitor()];
    }
    public function enableSandbox() : void
    {
        $this->sandboxed = \true;
    }
    public function disableSandbox() : void
    {
        $this->sandboxed = \false;
    }
    public function isSandboxed() : bool
    {
        return $this->sandboxedGlobally || $this->sandboxed;
    }
    public function isSandboxedGlobally() : bool
    {
        return $this->sandboxedGlobally;
    }
    public function setSecurityPolicy(\WPR\Vendor\Twig\Sandbox\SecurityPolicyInterface $policy)
    {
        $this->policy = $policy;
    }
    public function getSecurityPolicy() : \WPR\Vendor\Twig\Sandbox\SecurityPolicyInterface
    {
        return $this->policy;
    }
    public function checkSecurity($tags, $filters, $functions) : void
    {
        if ($this->isSandboxed()) {
            $this->policy->checkSecurity($tags, $filters, $functions);
        }
    }
    public function checkMethodAllowed($obj, $method, int $lineno = -1, \WPR\Vendor\Twig\Source $source = null) : void
    {
        if ($this->isSandboxed()) {
            try {
                $this->policy->checkMethodAllowed($obj, $method);
            } catch (\WPR\Vendor\Twig\Sandbox\SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);
                throw $e;
            }
        }
    }
    public function checkPropertyAllowed($obj, $method, int $lineno = -1, \WPR\Vendor\Twig\Source $source = null) : void
    {
        if ($this->isSandboxed()) {
            try {
                $this->policy->checkPropertyAllowed($obj, $method);
            } catch (\WPR\Vendor\Twig\Sandbox\SecurityNotAllowedPropertyError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);
                throw $e;
            }
        }
    }
    public function ensureToStringAllowed($obj, int $lineno = -1, \WPR\Vendor\Twig\Source $source = null)
    {
        if ($this->isSandboxed() && \is_object($obj) && \method_exists($obj, '__toString')) {
            try {
                $this->policy->checkMethodAllowed($obj, '__toString');
            } catch (\WPR\Vendor\Twig\Sandbox\SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);
                throw $e;
            }
        }
        return $obj;
    }
}
