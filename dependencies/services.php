<?php

// Exit if accessed directly.
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

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
