<?php

declare(strict_types=1);

use WPR\Service\ConfigService;

/**
 * @var string $content
 */

if (! defined('ABSPATH')) {
    exit;
}

$textDomain = ConfigService::PLUGIN_NAME;
?>
<div class="wrap">
    <a href="?page=wpr-settings" class="page-title-action"><?php esc_html_e('Settings', $textDomain); ?></a> &nbsp;
    <h1 class="wp-heading-inline">
        <?php esc_html_e('Stars rating list', $textDomain); ?>
    </h1>
    <div id="wpr-wp-ratings-list-table">
        <div id="wpr-post-body">
            <form id="wpr-list-form" method="post">
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WP_List_Table HTML.
                echo $content;
                ?>
            </form>
        </div>
    </div>
</div>
