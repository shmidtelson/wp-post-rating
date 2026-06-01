<?php

declare(strict_types=1);

use WPR\Service\ConfigService;

if (! defined('ABSPATH')) {
    exit;
}
?>
<a href="options-general.php?page=wpr-settings">
    <?php esc_html_e('Settings', ConfigService::PLUGIN_NAME); ?>
</a>
