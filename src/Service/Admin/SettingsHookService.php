<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use WPR\Service\ScriptsService;
use WPR\Service\SettingService;
use WPR_Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WPR\Views\Admin\MenuItemView;
use WPR\Views\Admin\SettingsView;
use WPR\Service\SettingFormService;
use WPR\Views\Admin\RatingTableView;
use WPR\Service\TwigEnvironmentService;
use WPR\Abstractions\Interfaces\HookServiceInterface;

class SettingsHookService implements HookServiceInterface
{
    /**
     * @var ScriptsService
     */
    private $scriptsService;

    /**
     * @var MenuItemView
     */
    private $menuItemView;

    /**
     * @var AdminMenuService
     */
    private $adminMenuService;

    /**
     * @var SettingService
     */
    private $settingService;

    /**
     * @var SettingFormService
     */
    private $settingFormService;

    /**
     * @var TwigEnvironmentService
     */
    private $twigService;

    /**
     * @var RatingTableView
     */
    private $ratingTableView;

    /**
     * @var SettingsView
     */
    private $settingsView;
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(
        ScriptsService $scriptsService,
        MenuItemView $menuItemView,
        AdminMenuService $adminMenuService,
        SettingService $settingService,
        SettingFormService $settingFormService,
        TwigEnvironmentService $twigService,
        RatingTableView $ratingTableView,
        SettingsView $settingsView,
    ParameterBagInterface $params
       ) {
        $this->scriptsService = $scriptsService;
        $this->menuItemView = $menuItemView;
        $this->adminMenuService = $adminMenuService;
        $this->settingService = $settingService;
        $this->settingFormService = $settingFormService;
        $this->twigService = $twigService;
        $this->ratingTableView = $ratingTableView;
        $this->settingsView = $settingsView;
        $this->params = $params;
    }

    public function hooks(): void
    {
        // Admin Scripts
        add_action('admin_enqueue_scripts', [$this->scriptsService, 'initAdminScripts']);

        // Add settings link
        add_filter('plugin_action_links_'.$this->params->get('wpr.base_name'), [$this->menuItemView, 'addSettingsLinkToPluginList']);

        // Settings
        add_action('admin_init', [$this->settingService, 'setDefaultSettings']);

        // Settings save form
        add_action('admin_post_wpr-update', [$this->settingFormService, 'saveForm']);
        add_action('admin_notices', [$this->settingFormService, 'successMessage']);

        // Add settings page to admin menu
        add_action('admin_menu', [$this->adminMenuService, 'addMenuSection']);

        // Add stars to Plugin list near our plugin
        add_filter('plugin_row_meta', [$this->adminMenuService, 'addStarsNearPluginName'], 10, 2);
    }
}
