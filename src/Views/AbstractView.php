<?php

declare(strict_types=1);

namespace WPR\Views;

use DI\Container;
use WPR\Service\ConfigService;
use WPR\Twig\TwigInitEnvironment;

class AbstractView
{
    protected $twig;

    protected $config;

    public function __construct()
    {
        $this->twig = TwigInitEnvironment::getTwigEnvironment();
        $this->config = (new Container())->get(ConfigService::class);
    }
}
