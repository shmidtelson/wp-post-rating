<?php

// Exit if accessed directly.
use WPR\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use WPR\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use WPR\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use function WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\service;
use WPR\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

if (! defined('ABSPATH')) {
    exit;
}

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
                             ->defaults()
                             ->autowire()
                             ->autoconfigure()
                             ->public();

    $services->load('WPR\\', '../src')
             ->exclude('../src/{Entity,Dto,Plugin.php}')
             ->public();

    $services->set('parameter_bag', ContainerBag::class)
             ->args([
                 service('service_container'),
             ])
             ->alias(ContainerBagInterface::class, 'parameter_bag')
             ->alias(ParameterBagInterface::class, 'parameter_bag');
};
