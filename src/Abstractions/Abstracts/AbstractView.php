<?php

declare(strict_types=1);

namespace WPR\Abstractions\Abstracts;

use WPR\Service\TwigEnvironmentService;
use WPR\Vendor\Psr\Container\ContainerInterface;

class AbstractView
{
    protected $twig;

    protected $container;

    public function __construct(TwigEnvironmentService $twigService, ContainerInterface $container)
    {
        $this->twig = $twigService;
        $this->container = $container;
    }
}
