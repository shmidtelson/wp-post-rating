<?php

declare(strict_types=1);

namespace WPR\Service\Admin;

use DI\Container;
use WPR\Service\ConfigService;
use WPR\Views\Admin\RatingTableView;

class AdminMenuService
{
    public function addMenuSection()
    {
        add_submenu_page(
            'options-general.php',
            '🟊 '.__('Stars rating', ConfigService::PLUGIN_NAME), //page title
            '🟊 '.__('Stars rating', ConfigService::PLUGIN_NAME), //menu title
            'manage_options', //capability
            ConfigService::PLUGIN_NAME, //menu_slug,
            [(new Container())->get(RatingTableView::class), 'loadRatingTable']
        );
    }
}
