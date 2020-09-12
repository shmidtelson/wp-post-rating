<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use WPR\Service\ScriptsService;
use WPR\Service\SettingService;
use WPR\Views\Admin\MenuItemView;
use WPR\Service\SettingFormService;
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
        // Admin Scripts
        add_action('admin_enqueue_scripts', [$this->container->get(ScriptsService::class), 'initAdminScripts']);
        // Add settings link
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), [$this->container->get(MenuItemView::class), 'addSettingsLinkToPluginList']);
        // Add settings page to admin menu
        add_action('admin_menu', [$this->container->get(AdminMenuService::class), 'addMenuSection']);

        // Settings
        add_action('admin_init', [$this->container->get(SettingService::class), 'setDefaultSettings']);
        // Settings save form
        add_action('admin_post_wpr-update', [$this->container->get(SettingFormService::class), 'saveForm']);
        add_action('admin_notices', [$this->container->get(SettingFormService::class), 'successMessage']);
    }
}
