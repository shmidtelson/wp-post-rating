<?php

declare(strict_types=1);

use WPR\Service\ConfigService;

if (! defined('ABSPATH')) {
    exit;
}
?>
<span class="dashicons dashicons-star-filled" style="font-size: 12px;width: 12px;height: 12px;top: 3px;position: relative;"></span> <?php esc_html_e('Stars rating', ConfigService::PLUGIN_NAME); ?>
