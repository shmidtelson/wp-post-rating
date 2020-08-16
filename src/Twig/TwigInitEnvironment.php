<?php

declare(strict_types=1);

namespace WPR\Twig;

use DI\Container;
use Twig\Environment;
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

        $twig->addExtension(new TransExtension());

        return $twig;
    }
}
