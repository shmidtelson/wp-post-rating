<?php
declare(strict_types=1);

namespace WPR\Views;

use DI\Container;
use WPR\Service\ConfigService;
use WPR\Twig\TransExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AbstractView
{
    protected $twig;
    protected $config;

    public function __construct()
    {
        $this->twig   = $this->getTwigEnvironment();
        $this->config = (new Container())->get(ConfigService::class);
    }

    private function getTwigEnvironment()
    {
        $twig = new Environment(
            new FilesystemLoader(
                (new Container())->get(ConfigService::class)->getPluginPath() . '/views')
        );

        $twig->addExtension(new TransExtension());

        return $twig;
    }
}