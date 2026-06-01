<?php

declare(strict_types=1);

/**
 * Loads WP_List_Table for Symfony DI compile and admin use.
 * Does not load template.php or define convert_to_screen() (WordPress provides that later).
 */
function wpr_load_list_table_dependencies(): void
{
    if (! class_exists('WP_Screen')) {
        require_once ABSPATH.'wp-admin/includes/class-wp-screen.php';
    }

    if (! class_exists('WP_List_Table')) {
        require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
    }
}
