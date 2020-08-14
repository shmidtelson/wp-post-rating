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
    protected $config;

    public function __construct()
    {
        $this->twig = new Environment(
            new FilesystemLoader(
                (new Container())->get(ConfigService::class)->getPluginPath() . '/views')
        );
        $this->config = (new Container())->get(ConfigService::class);
    }
}