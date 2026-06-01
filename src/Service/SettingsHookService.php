<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Views\NonceView;
use WPR\Abstractions\Interfaces\HookServiceInterface;

class SettingsHookService implements HookServiceInterface
{
    /**
     * @var ScriptsService
     */
    private $scriptsService;

    /**
     * @var NonceView
     */
    private $nonceView;

    /**
     * @var PostContentHookService
     */
    private $postContentHookService;

    public function __construct(
        ScriptsService $scriptsService,
        NonceView $nonceView,
        PostContentHookService $postContentHookService
    ) {
        $this->scriptsService = $scriptsService;
        $this->nonceView = $nonceView;
        $this->postContentHookService = $postContentHookService;
    }

    public function hooks(): void
    {
        add_action('wp_enqueue_scripts', [$this->scriptsService, 'initScripts']);
        add_action('wp_head', [$this->nonceView, 'render']);
        add_filter('the_content', [$this->postContentHookService, 'filterPostContent']);
    }
}
