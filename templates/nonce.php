<?php

declare(strict_types=1);

/**
 * @var string $nonceKey
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<meta name="_wpr_nonce" content="<?php echo esc_attr($nonceKey); ?>" />
