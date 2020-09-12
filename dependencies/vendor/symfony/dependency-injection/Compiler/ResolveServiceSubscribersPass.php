<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Symfony\Component\DependencyInjection\Compiler;

use WPR\Vendor\Psr\Container\ContainerInterface;
use WPR\Vendor\Symfony\Component\DependencyInjection\Definition;
use WPR\Vendor\Symfony\Component\DependencyInjection\Reference;
use WPR\Vendor\Symfony\Contracts\Service\ServiceProviderInterface;
/**
 * Compiler pass to inject their service locator to service subscribers.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ResolveServiceSubscribersPass extends \WPR\Vendor\Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass
{
    private $serviceLocator;
    protected function processValue($value, bool $isRoot = \false)
    {
        if ($value instanceof \WPR\Vendor\Symfony\Component\DependencyInjection\Reference && $this->serviceLocator && \in_array((string) $value, [\WPR\Vendor\Psr\Container\ContainerInterface::class, \WPR\Vendor\Symfony\Contracts\Service\ServiceProviderInterface::class], \true)) {
            return new \WPR\Vendor\Symfony\Component\DependencyInjection\Reference($this->serviceLocator);
        }
        if (!$value instanceof \WPR\Vendor\Symfony\Component\DependencyInjection\Definition) {
            return parent::processValue($value, $isRoot);
        }
        $serviceLocator = $this->serviceLocator;
        $this->serviceLocator = null;
        if ($value->hasTag('container.service_subscriber.locator')) {
            $this->serviceLocator = $value->getTag('container.service_subscriber.locator')[0]['id'];
            $value->clearTag('container.service_subscriber.locator');
        }
        try {
            return parent::processValue($value);
        } finally {
            $this->serviceLocator = $serviceLocator;
        }
    }
}
