<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use WPR\Service\ConfigService;
use WPR\Views\Admin\SettingsView;
use WPR\Views\Admin\RatingTableView;
use WPR\Service\TwigEnvironmentService;
use WPR\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdminMenuService
{
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
        TwigEnvironmentService $twigService,
        RatingTableView $ratingTableView,
        SettingsView $settingsView,
        ParameterBagInterface $params
    ) {
        $this->twigService = $twigService;
        $this->ratingTableView = $ratingTableView;
        $this->settingsView = $settingsView;
        $this->params = $params;
    }

    public function addMenuSection()
    {
        add_submenu_page(
            'options-general.php',
            $this->twigService->getTwig()->render('admin/menu/stars-menu.twig'),
            $this->twigService->getTwig()->render('admin/menu/stars-menu.twig'),
            'manage_options', //capability
            ConfigService::PLUGIN_NAME, //menu_slug,
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

    public function addStarsNearPluginName($links, $file)
    {
        if ($this->params->get('wpr.base_name') === $file) {
            $row_meta = [
                'Rate me' => $this->twigService->getTwig()->render('admin/menu/stars-in-plugin-list.twig'),
            ];

            return array_merge($links, $row_meta);
        }

        return (array) $links;
    }
}
