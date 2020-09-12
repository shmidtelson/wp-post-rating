<?php

declare(strict_types=1);

namespace WPR\Service;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use WPR\Abstractions\Abstracts\AbstractService;

class TwigEnvironmentService extends AbstractService
{
    public function getTwig()
    {
        $twig = new Environment(
            new FilesystemLoader(
                $this->container->get(ConfigService::class)->getPluginPath().'/views')
        );
        $twig->addGlobal('PLUGIN_NAME', ConfigService::PLUGIN_NAME);
        $twig->addFunction(new TwigFunction('__', '__'));

        return $twig;
    }
}
