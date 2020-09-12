<?php

namespace WPR\Views;

use WPR\Service\ConfigService;
use WPR\Service\TwigEnvironmentService;

class NonceView
{
    /**
     * @var TwigEnvironmentService
     */
    private $twigService;

    public function __construct(TwigEnvironmentService $twigService)
    {
        $this->twigService = $twigService;
    }

    public function render(): void
    {
        echo $this->twigService->getTwig()->render('nonce.twig', [
            'nonceKey' => wp_create_nonce(ConfigService::PLUGIN_NONCE_KEY),
        ]);
    }
}
