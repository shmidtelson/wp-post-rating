<?php

namespace WPR\Views;

use WPR\Abstractions\Abstracts\AbstractView;
use WPR\Service\ConfigService;

class NonceView extends AbstractView
{
    public function render(): void
    {
        echo $this->twig->getTwig()->render('nonce.twig', [
            'nonceKey' => wp_create_nonce(ConfigService::PLUGIN_NONCE_KEY),
        ]);
    }
}
