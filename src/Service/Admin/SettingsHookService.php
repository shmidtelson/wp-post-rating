<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use WPR\Abstractions\Interfaces\HookServiceInterface;
use WPR\Service\PluginContext;
use WPR\Service\ScriptsService;
use WPR\Service\SettingFormService;
use WPR\Service\SettingService;
use WPR\Views\Admin\MenuItemView;

class SettingsHookService implements HookServiceInterface
{
    public function __construct(
        private readonly ScriptsService $scriptsService,
        private readonly MenuItemView $menuItemView,
        private readonly AdminMenuService $adminMenuService,
        private readonly SettingService $settingService,
        private readonly SettingFormService $settingFormService,
        private readonly PluginContext $context,
    ) {
    }

    public function hooks(): void
    {
        add_action('admin_enqueue_scripts', [$this->scriptsService, 'initAdminScripts']);

        add_filter(
            'plugin_action_links_' . $this->context->basename,
            [$this->menuItemView, 'addSettingsLinkToPluginList']
        );

        add_action('admin_init', [$this->settingService, 'setDefaultSettings']);

        add_action('admin_post_wpr-update', [$this->settingFormService, 'saveForm']);
        add_action('admin_notices', [$this->settingFormService, 'successMessage']);

        add_action('admin_menu', [$this->adminMenuService, 'addMenuSection']);

        add_filter('plugin_row_meta', [$this->adminMenuService, 'addStarsNearPluginName'], 10, 2);
    }
}
