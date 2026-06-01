<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use WPR\Service\ConfigService;
use WPR\Service\PluginContext;
use WPR\Template\TemplateRenderer;
use WPR\Views\Admin\RatingTableView;
use WPR\Views\Admin\SettingsView;

class AdminMenuService
{
    public function __construct(
        private readonly TemplateRenderer $templates,
        private readonly RatingTableView $ratingTableView,
        private readonly SettingsView $settingsView,
        private readonly PluginContext $context,
    ) {
    }

    public function addMenuSection(): void
    {
        $menuTitle = $this->templates->render('admin/menu/stars-menu');

        add_submenu_page(
            'options-general.php',
            $menuTitle,
            $menuTitle,
            'manage_options',
            ConfigService::PLUGIN_NAME,
            [$this->ratingTableView, 'loadRatingTable']
        );

        add_submenu_page(
            null,
            __('WPR Settings', ConfigService::PLUGIN_NAME),
            __('WPR Settings', ConfigService::PLUGIN_NAME),
            'manage_options',
            ConfigService::OPTIONS_KEY,
            [$this->settingsView, 'addOptionsPage']
        );
    }

    /**
     * @param array<int, string> $links
     * @param string             $file
     * @return array<int, string>
     */
    public function addStarsNearPluginName(array $links, string $file): array
    {
        if ($this->context->basename === $file) {
            $row_meta = [
                'Rate me' => $this->templates->render('admin/menu/stars-in-plugin-list'),
            ];

            return array_merge($links, $row_meta);
        }

        return $links;
    }
}
