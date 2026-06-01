<?php

declare(strict_types=1);

use WPR\Entity\SettingEntity;
use WPR\Service\ConfigService;

/**
 * @var SettingEntity $options
 * @var string        $formHiddenField
 * @var string        $formSubmitButton
 * @var string        $formActionLink
 */

if (! defined('ABSPATH')) {
    exit;
}

$textDomain = ConfigService::PLUGIN_NAME;
?>
<div class="wrap">
    <form method="post" action="<?php echo esc_url($formActionLink); ?>">
        <a href="?page=wp-post-rating"
           class="page-title-action"><?php esc_html_e('All votes', $textDomain); ?></a> &nbsp;

        <h1 class="wp-heading-inline"><?php esc_html_e('Star rating settings', $textDomain); ?></h1>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="wpr-position"><?php esc_html_e('Display on single posts', $textDomain); ?></label>
                </th>
                <td>
                    <fieldset>
                        <select name="position" id="wpr-position">
                            <option value="shortcode" <?php selected($options->getPosition(), 'shortcode'); ?>>
                                <?php esc_html_e('Manual only (shortcode / PHP)', $textDomain); ?>
                            </option>
                            <option value="before" <?php selected($options->getPosition(), 'before'); ?>>
                                <?php esc_html_e('Before post content', $textDomain); ?>
                            </option>
                            <option value="after" <?php selected($options->getPosition(), 'after'); ?>>
                                <?php esc_html_e('After post content', $textDomain); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php esc_html_e('Applies to single blog posts. Before/after skips the shortcode in content.', $textDomain); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Shortcodes', $textDomain); ?></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span><?php esc_html_e('Shortcodes', $textDomain); ?></span>
                        </legend>
                        <div class="shortcode-checked-js">
                            <p class="description" id="tagline-description">
                                <?php esc_html_e('Use when display mode is manual, or for pages and custom placement', $textDomain); ?>
                            </p>
                            <p>
                                <b><?php esc_html_e('Display in content', $textDomain); ?></b>
                                <input class="regular-text" value="[wp_rating]" onclick="select()" />
                                <input class="regular-text" value="echo do_shortcode('[wp_rating]');"
                                       onclick="select()" />
                            </p>
                            <p>
                                <b><?php esc_html_e('Display total votes for current post', $textDomain); ?></b>
                                <input class="regular-text" value="[wp_rating_total]"
                                       onclick="select()" />
                                <input class="regular-text" value="echo do_shortcode('[wp_rating_total]');"
                                       onclick="select()" />
                            </p>
                            <p>
                                <b><?php esc_html_e('Display average votes for current post', $textDomain); ?></b>
                                <input class="regular-text" value="[wp_rating_avg]"
                                       onclick="select()" />
                                <input class="regular-text" value="echo do_shortcode('[wp_rating_avg]');"
                                       onclick="select()" />
                            </p>
                        </div>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"></th>
                <td>
                    <fieldset>
                        <div class="wpr-wrapp wpr-wrapp-admin"><span></span></div>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Stars color', $textDomain); ?>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span><?php esc_html_e('Stars color', $textDomain); ?></span>
                        </legend>
                        <input class="color_chooser_js" name="main_color" type="text"
                               value="<?php echo esc_attr($options->getStarsMainColor()); ?>" />
                        <p class="colors-description" id="colors-description">
                            <?php esc_html_e('Second (darker) color choose automatically', $textDomain); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Text (border) color', $textDomain); ?>
                </th>
                <td>
                    <fieldset>
                        <input class="text_color_chooser_js" name="text_color" type="text"
                               value="<?php echo esc_attr($options->getStarsTextColor()); ?>" />
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Tooltip background color', $textDomain); ?>
                </th>
                <td>
                    <fieldset>
                        <input class="text_background_color_chooser_js" name="text_background_color" type="text"
                               value="<?php echo esc_attr($options->getStarsTextBackgroundColor()); ?>" />
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Enable schema markup', $textDomain); ?>
                </th>
                <td>
                    <fieldset>
                        <input name="schema_enable"
                           type="checkbox"
                           value="1"
                           <?php checked($options->isSchemaEnable()); ?>
                        />
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WP core form helpers.
        echo $formSubmitButton;
        echo $formHiddenField;
        ?>
        <input type="hidden" name="action" value="wpr-update">
    </form>
</div>
