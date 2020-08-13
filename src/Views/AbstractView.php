<?php
declare(strict_types=1);

namespace WPR\Views;

use DI\Container;
use WPR\Service\ConfigService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AbstractView
{
    protected $twig;
    protected $container;

    public function __construct()
    {
        $this->container = new Container();
        ;
        $this->twig = new Environment(
            new FilesystemLoader(
                $this->container->get(ConfigService::class)->getPluginPath() . '/views')
        );
    }
}