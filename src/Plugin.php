<?php

declare(strict_types=1);

namespace WPR;

use Exception;
use WPR\Service\AjaxService;
use WPR\Views\RatingView;
use WPR\Service\WidgetService;
use WPR\Service\TranslateService;
use WPR\Service\MaintenanceService;
use WPR\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;

class Plugin
{
    /**
     * Dependency Injection Container.
     *
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * Plugin constructor.
     *
     * @param ContainerBuilder $containerBuilder Dependency Injection Container.
     */
    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * Run plugin.
     *
     * @throws \Exception Object doesn't exist.
     */
    public function run(): void
    {
        // Load translates
        add_action('init', [$this->containerBuilder->get(TranslateService::class), 'loadPluginTextDomain']);

        // Start install tables if not exists
        register_activation_hook(__FILE__, [$this->containerBuilder->get(MaintenanceService::class), 'installPlugin']);

        // Add shortcodes
        add_shortcode('wp_rating', [$this->containerBuilder->get(RatingView::class), 'renderStars']);
        add_shortcode('wp_rating_total', [$this->containerBuilder->get(RatingView::class), 'getRatingTotal']);
        add_shortcode('wp_rating_avg', [$this->containerBuilder->get(RatingView::class), 'getRatingAvg']);

        // Add widgets
        add_action('widgets_init', [$this->containerBuilder->get(WidgetService::class), 'loadWidget']);

        // Add ajax
        add_action('wp_ajax_nopriv_wpr_voted', [$this->containerBuilder->get(AjaxService::class), 'actionVote']);
        add_action('wp_ajax_wpr_voted', [$this->containerBuilder->get(AjaxService::class), 'actionVote']);

        // Init special hooks
        is_admin() ? $this->runAdmin() : $this->runFront();
    }

    /**
     * Run admin part.
     *
     * @throws Exception Object doesn't exist.
     */
    private function runAdmin(): void
    {
        $this->containerBuilder->get(\WPR\Service\Admin\SettingsHookService::class)->hooks();
    }

    /**
     * Run frontend part.
     *
     * @throws Exception Object doesn't exist.
     */
    private function runFront(): void
    {
        $this->containerBuilder->get(\WPR\Service\SettingsHookService::class)->hooks();
    }
}
