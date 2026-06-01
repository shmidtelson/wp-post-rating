<?php

declare(strict_types=1);

namespace WPR;

use WPR\Bootstrap\PluginBootstrap;
use WPR\Service\Admin\SettingsHookService as AdminSettingsHookService;
use WPR\Service\AjaxService;
use WPR\Service\SettingsHookService as FrontSettingsHookService;
use WPR\Service\TranslateService;
use WPR\Views\RatingView;
use WPR\Wordpress\WPR_Widget;

class Plugin
{
    public function __construct(
        private readonly PluginBootstrap $bootstrap,
    ) {
    }

    public function run(): void
    {
        add_action('init', [$this->bootstrap->get(TranslateService::class), 'loadPluginTextDomain'], 0);

        $ratingView = $this->bootstrap->get(RatingView::class);
        add_shortcode('wp_rating', [$ratingView, 'renderStars']);
        add_shortcode('wp_rating_total', [$ratingView, 'getRatingTotal']);
        add_shortcode('wp_rating_avg', [$ratingView, 'getRatingAvg']);

        add_action('widgets_init', function (): void {
            register_widget($this->bootstrap->get(WPR_Widget::class));
        });

        add_action('wp_ajax_nopriv_wpr_voted', [$this->bootstrap->get(AjaxService::class), 'actionVote']);
        add_action('wp_ajax_wpr_voted', [$this->bootstrap->get(AjaxService::class), 'actionVote']);

        is_admin()
            ? $this->bootstrap->get(AdminSettingsHookService::class)->hooks()
            : $this->bootstrap->get(FrontSettingsHookService::class)->hooks();
    }
}
