<?php

declare(strict_types=1);

/**
 * @var int|string $id
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<input type="checkbox" name="id[]" value="<?php echo esc_attr((string) $id); ?>" />
