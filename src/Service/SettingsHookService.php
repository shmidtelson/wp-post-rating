<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Views\NonceView;
use WPR\Abstractions\Interfaces\HookServiceInterface;

class SettingsHookService implements HookServiceInterface
{
    private $scriptsService;

    private $nonceView;

    public function __construct(ScriptsService $scriptsService, NonceView $nonceView)
    {
        $this->scriptsService = $scriptsService;
        $this->nonceView = $nonceView;
    }

    public function hooks(): void
    {
        // Include js and css
        add_action('wp_enqueue_scripts', [$this->scriptsService, 'initScripts']);
        // Add nonce to head
        add_action('wp_head', [$this->nonceView, 'render']);
    }
}
