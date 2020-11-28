<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Extension;

use WPR_Vendor\Twig\TwigFunction;
final class StringLoaderExtension extends \WPR_Vendor\Twig\Extension\AbstractExtension
{
    public function getFunctions() : array
    {
        return [new \WPR_Vendor\Twig\TwigFunction('template_from_string', 'twig_template_from_string', ['needs_environment' => \true])];
    }
}
namespace WPR_Vendor;

use WPR_Vendor\Twig\Environment;
use WPR_Vendor\Twig\TemplateWrapper;
/**
 * Loads a template from a string.
 *
 *     {{ include(template_from_string("Hello {{ name }}")) }}
 *
 * @param string $template A template as a string or object implementing __toString()
 * @param string $name     An optional name of the template to be used in error messages
 */
function twig_template_from_string(\WPR_Vendor\Twig\Environment $env, $template, string $name = null) : \WPR_Vendor\Twig\TemplateWrapper
{
    return $env->createTemplate((string) $template, $name);
}
