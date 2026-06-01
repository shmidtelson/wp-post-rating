<?php

declare(strict_types=1);

/**
 * @var int    $postId
 * @var string $avgRating
 * @var int    $total
 * @var string $jsonMarkup
 * @var string $starsColor
 * @var string $textColor
 * @var string $backgroundColor
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div
    class="wpr-wrapp"
    id="wpr-widget-<?php echo esc_attr((string) $postId); ?>"
    data-id="<?php echo esc_attr((string) $postId); ?>"
    data-value="<?php echo esc_attr($avgRating); ?>"
    data-votestitle="<?php echo esc_attr(__('Votes', 'wp-post-rating')); ?>"
    data-total="<?php echo esc_attr((string) $total); ?>"
    data-fontsize="30px"
    data-starscolor="<?php echo esc_attr($starsColor); ?>"
    data-textcolor="<?php echo esc_attr($textColor); ?>"
    data-backgroundcolor="<?php echo esc_attr($backgroundColor); ?>"
></div>
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- schema JSON-LD from trusted plugin output.
echo $jsonMarkup;
