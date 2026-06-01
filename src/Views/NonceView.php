<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\ConfigService;
use WPR\Template\TemplateRenderer;

class NonceView
{
    public function __construct(
        private readonly TemplateRenderer $templates,
    ) {
    }

    public function render(): void
    {
        echo $this->templates->render('nonce', [
            'nonceKey' => wp_create_nonce(ConfigService::PLUGIN_NONCE_KEY),
        ]);
    }
}
