<?php

declare(strict_types=1);

namespace WPR\Abstractions\Interfaces;

use Psr\Container\ContainerInterface;

interface ServiceInterface
{
    public function __construct(ContainerInterface $container);
}
