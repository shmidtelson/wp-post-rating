<?php

declare(strict_types=1);

/**
 * @var string      $title
 * @var string|false $thumbnail
 * @var int|string  $ratingCount
 * @var string      $ratingAvg
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "Product",
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?php echo esc_js($ratingAvg); ?>",
        "ratingCount": "<?php echo esc_js((string) $ratingCount); ?>",
        "worstRating": "1",
        "bestRating": "5"
    },
    <?php if ($thumbnail) : ?>
    "image": "<?php echo esc_url($thumbnail); ?>",
    <?php endif; ?>
    "name": "<?php echo esc_js($title); ?>"
}
</script>
