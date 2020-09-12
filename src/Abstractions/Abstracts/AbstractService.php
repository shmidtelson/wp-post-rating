<?php

declare(strict_types=1);

namespace WPR\Abstractions\Abstracts;

use WPR\Abstractions\Interfaces\ServiceInterface;
use WPR\Vendor\Psr\Container\ContainerInterface;

abstract class AbstractService implements ServiceInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
