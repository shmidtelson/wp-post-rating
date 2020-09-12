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

use WPR\Vendor\Twig\TwigFunction;
final class StringLoaderExtension extends \WPR\Vendor\Twig\Extension\AbstractExtension
{
    public function getFunctions() : array
    {
        return [new \WPR\Vendor\Twig\TwigFunction('template_from_string', 'twig_template_from_string', ['needs_environment' => \true])];
    }
}
namespace WPR\Vendor;

use WPR\Vendor\Twig\Environment;
use WPR\Vendor\Twig\TemplateWrapper;
/**
 * Loads a template from a string.
 *
 *     {{ include(template_from_string("Hello {{ name }}")) }}
 *
 * @param string $template A template as a string or object implementing __toString()
 * @param string $name     An optional name of the template to be used in error messages
 */
function twig_template_from_string(\WPR\Vendor\Twig\Environment $env, $template, string $name = null) : \WPR\Vendor\Twig\TemplateWrapper
{
    return $env->createTemplate((string) $template, $name);
}
