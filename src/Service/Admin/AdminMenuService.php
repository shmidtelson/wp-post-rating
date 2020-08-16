<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use DI\Container;
use WPR\Service\ConfigService;
use WPR\Twig\TwigInitEnvironment;
use WPR\Views\Admin\RatingTableView;
use WPR\Views\Admin\SettingsView;

class AdminMenuService
{
    private $twig;

    public function __construct()
    {
        $this->twig = TwigInitEnvironment::getTwigEnvironment();
    }
    public function addMenuSection()
    {
        add_submenu_page(
            'options-general.php',
            $this->twig->render('admin/menu/stars-menu.twig'),
            $this->twig->render('admin/menu/stars-menu.twig'),
            'manage_options', //capability
            ConfigService::PLUGIN_NAME, //menu_slug,
            [(new Container())->get(RatingTableView::class), 'loadRatingTable']
        );

        add_submenu_page(
            null,
            __('WPR Settings', ConfigService::PLUGIN_NAME),
            __('WPR Settings', ConfigService::PLUGIN_NAME),
            'manage_options',
            ConfigService::OPTIONS_KEY,
            [(new Container())->get(SettingsView::class), 'addOptionsPage']
        );
    }
}
