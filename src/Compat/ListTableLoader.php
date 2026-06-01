<?php

declare(strict_types=1);

namespace WPR\Compat;

/**
 * Loads WP_List_Table for admin list screens.
 * Does not load template.php or define convert_to_screen() (WordPress provides that later).
 */
final class ListTableLoader
{
    public static function loadDependencies(): void
    {
        if (! class_exists('WP_Screen')) {
            require_once ABSPATH.'wp-admin/includes/class-wp-screen.php';
        }

        if (! class_exists('WP_List_Table')) {
            require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
        }
    }
}
