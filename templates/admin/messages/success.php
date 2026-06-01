<?php

declare(strict_types=1);

/**
 * @var string $content
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="notice notice-success is-dismissible">
    <p>
        <?php echo esc_html($content); ?>
    </p>
</div>
