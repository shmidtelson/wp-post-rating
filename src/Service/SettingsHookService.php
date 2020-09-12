<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Views\NonceView;
use WPR\Vendor\Psr\Container\ContainerInterface;
use WPR\Abstractions\Interfaces\HookServiceInterface;

class SettingsHookService implements HookServiceInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function hooks(): void
    {
        // Include js and css
        add_action('wp_enqueue_scripts', [$this->container->get(ScriptsService::class), 'initScripts']);
        // Add nonce to head
        add_action('wp_head', [$this->container->get(NonceView::class), 'render']);
    }
}
