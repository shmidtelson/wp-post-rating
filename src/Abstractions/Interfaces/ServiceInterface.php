<?php

declare(strict_types=1);

namespace WPR\Abstractions\Interfaces;

use WPR\Vendor\Psr\Container\ContainerInterface;

interface ServiceInterface
{
    public function __construct(ContainerInterface $container);
}
