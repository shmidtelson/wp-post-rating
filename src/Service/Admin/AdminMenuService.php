<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use WPR\Service\ConfigService;
use WPR\Service\TwigEnvironmentService;
use WPR\Views\Admin\SettingsView;
use WPR\Views\Admin\RatingTableView;
use WPR\Abstractions\Abstracts\AbstractService;

class AdminMenuService extends AbstractService
{
    public function addMenuSection()
    {
        /**
         * @var TwigEnvironmentService $twig
         */
        $twig = $this->container->get(TwigEnvironmentService::class);

        add_submenu_page(
            'options-general.php',
            $twig->getTwig()->render('admin/menu/stars-menu.twig'),
            $twig->getTwig()->render('admin/menu/stars-menu.twig'),
            'manage_options', //capability
            ConfigService::PLUGIN_NAME, //menu_slug,
           [$this->container->get(RatingTableView::class), 'loadRatingTable']
        );

        add_submenu_page(
            null,
            __('WPR Settings', ConfigService::PLUGIN_NAME),
            __('WPR Settings', ConfigService::PLUGIN_NAME),
            'manage_options',
            ConfigService::OPTIONS_KEY,
            [$this->container->get(SettingsView::class), 'addOptionsPage']
        );
    }
}
