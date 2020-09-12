<?php

// Exit if accessed directly.
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
             ->exclude('../src/{Entity,Dto,Plugin.php}');
};
