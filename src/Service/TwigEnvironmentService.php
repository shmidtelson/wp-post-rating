<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR_Vendor\Twig\Environment;
use WPR_Vendor\Twig\TwigFunction;
use WPR_Vendor\Twig\Loader\FilesystemLoader;

class TwigEnvironmentService
{
    /**
     * @var ConfigService
     */
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function getTwig()
    {
        $twig = new Environment(
            new FilesystemLoader(
                $this->configService->getPluginPath().'/views')
        );
        $twig->addGlobal('PLUGIN_NAME', ConfigService::PLUGIN_NAME);
        $twig->addFunction(new TwigFunction('__', '__'));

        return $twig;
    }
}
