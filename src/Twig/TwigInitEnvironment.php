<?php

declare(strict_types=1);

namespace WPR\Twig;

use DI\Container;
use Twig\Environment;
use Twig\TwigFunction;
use WPR\Service\ConfigService;
use Twig\Loader\FilesystemLoader;

class TwigInitEnvironment
{
    public static function getTwigEnvironment()
    {
        $twig = new Environment(
            new FilesystemLoader(
                (new Container())->get(ConfigService::class)->getPluginPath().'/views')
        );
        $twig->addGlobal('PLUGIN_NAME', ConfigService::PLUGIN_NAME);
        $twig->addFunction(new TwigFunction('__', '__'));
        return $twig;
    }
}
