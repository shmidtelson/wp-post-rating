<?php

namespace WPR_Plugin\Admin;

class Settings extends \WP_List_Table
{
    public $config;
    private $OPTION_SLUG = 'wpr-settings';

    public function __construct($config)
    {
        $this->config = $config;
        add_action('admin_init', [$this, 'wpr_register_settings']);
        add_action('admin_menu', [$this, 'wpr_register_options_page']);
        add_action('admin_enqueue_scripts', [$this, 'admin_page_scripts']);
        $this->change_hidden_menu();

    }

    public function wpr_register_settings()
    {
        // Rating position in content
        add_option('wpr_position', 'After');
        register_setting('wpr_options_group', 'wpr_position');
        register_setting('wpr_options_group', 'wpr_post_types');
    }

    public function wpr_register_options_page()
    {
        add_submenu_page(
            null,
            __('WPR Settings', $this->config->PLUGIN_NAME),
            __('WPR Settings', $this->config->PLUGIN_NAME),
            'manage_options',
            $this->OPTION_SLUG,
            [$this, 'wpr_options_page']
        );
    }

    public function wpr_options_page()
    {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <form method="post" action="options.php">
                <a href="?page=wp-post-rating"
                   class="page-title-action"><?php _e('All votes', $this->config->PLUGIN_NAME); ?></a>
                <?php settings_fields('wpr_options_group'); ?>
                <h1 class="wp-heading-inline"><?php _e('Star rating settings', $this->config->PLUGIN_NAME) ?></h1>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Star rating position in content', $this->config->PLUGIN_NAME) ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Star rating position in content', $this->config->PLUGIN_NAME) ?></span>
                                </legend>
                                <?php $this->setting_dropdown_fn([
                                    'before' => __('Before content', $this->config->PLUGIN_NAME),
                                    'after' => __('After content', $this->config->PLUGIN_NAME),
                                    'shortcode' => __('Shortcode', $this->config->PLUGIN_NAME),
                                ], 'wpr_position') ?>
                                <br>
                                <div class="shortcode-checked-js" style="display: none">
                                    <p class="description" id="tagline-description">
                                        <?=__('How to custom display rating widget', $this->config->PLUGIN_NAME)?>
                                    </p>
                                    <p>
                                        <b><?=__('Display in content', $this->config->PLUGIN_NAME)?></b>
                                        <input class="regular-text" value="[wp_rating]"  onclick="select()"/>
                                    </p>
                                    <p>
                                        <b><?=__('Display in PHP code', $this->config->PLUGIN_NAME)?></b>
                                        <input class="regular-text" value="echo do_shortcode('[wp_rating]');" onclick="select()"/>
                                    </p>
                                </div>
                            </fieldset>
                        </td>
                    </tr>

                    <!--                    <tr>-->
                    <!--                        <th scope="row">-->
                    <?php //_e('Showing on post types', $this->config->PLUGIN_NAME)
                    ?><!--</th>-->
                    <!--                        <td>-->
                    <!--                            <fieldset>-->
                    <!--                                <legend class="screen-reader-text">-->
                    <!--                                    <span>-->
                    <?php //_e('Showing on post types', $this->config->PLUGIN_NAME)
                    ?><!--</span>-->
                    <!--                                </legend>-->
                    <!--                                --><?php //$this->setting_chk1_fn([
                    //                                    'before' => __('Before content', $this->config->PLUGIN_NAME),
                    //                                    'after' => __('After content', $this->config->PLUGIN_NAME),
                    //                                ], 'wpr_post_types')
                    ?>
                    <!--                                <br>-->
                    <!--                            </fieldset>-->
                    <!--                        </td>-->
                    <!--                    </tr>-->
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Custom activate menu
     */
    public function change_hidden_menu()
    {
        if (isset($_GET['page']) and $_GET['page'] == 'wpr-settings') {
            add_action('admin_init', function () {
                global $submenu, $menu;

                foreach ($submenu['options-general.php'] as $key => $value) {
                    if ('wp-post-rating' == $value[2]) {
                        $submenu['options-general.php'][$key][4] = "current";
                    }
                }

                foreach ($menu as $key => $value) {
                    if ('options-general.php' == $value[2]) {
                        $menu[$key][4] .= " wp-menu-open";
                    }

                }
            });
        }
    }
    /**************************************************************
     * Buttons display methods
     */
    // Section HTML, displayed before the first option
//    public function section_text_fn()
//    {
//        echo '<p>Below are some examples of different option controls.</p>';
//    }

// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
    public function setting_dropdown_fn($items_list, $option_name)
    {
        $current_option = get_option($option_name);
        echo "<select id='{$option_name}' name='{$option_name}'>";
        foreach ($items_list as $key => $value) {
            $selected = ($current_option == $key) ? 'selected="selected"' : '';
            echo "<option value='$key' $selected>$value</option>";
        }
        echo "</select>";
    }

//// TEXTAREA - Name: plugin_options[text_area]
//    public function setting_textarea_fn()
//    {
//        $options = get_option('plugin_options');
//        echo "<textarea id='plugin_textarea_string' name='plugin_options[text_area]' rows='7' cols='50' type='textarea'>{$options['text_area']}</textarea>";
//    }
//
//// TEXTBOX - Name: plugin_options[text_string]
//    public function setting_string_fn()
//    {
//        $options = get_option('plugin_options');
//        echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
//    }
//
//// PASSWORD-TEXTBOX - Name: plugin_options[pass_string]
//    public function setting_pass_fn()
//    {
//        $options = get_option('plugin_options');
//        echo "<input id='plugin_text_pass' name='plugin_options[pass_string]' size='40' type='password' value='{$options['pass_string']}' />";
//    }
//
//// CHECKBOX - Name: plugin_options[chkbox1]
/// # TODO: ADD CUSTOM POST TYPES SELECT
    public function setting_chk1_fn($items_list, $option_name)
    {
        $options = get_option($option_name);
        if ($options['chkbox1']) {
            $checked = ' checked="checked" ';
        }
        foreach ($items_list as $key => $value) {
            echo '<label>';
            echo "<input " . $checked . " id='{$option_name}_{$key}' name='{$option_name}[]' type='checkbox' value='{$key}' />";
            echo '</label>';
        }

    }
//
//// CHECKBOX - Name: plugin_options[chkbox2]
//    public function setting_chk2_fn()
//    {
//        $options = get_option('plugin_options');
//        if ($options['chkbox2']) {
//            $checked = ' checked="checked" ';
//        }
//        echo "<input " . $checked . " id='plugin_chk2' name='plugin_options[chkbox2]' type='checkbox' />";
//    }
//
//// RADIO-BUTTON - Name: plugin_options[option_set1]
//    public function setting_radio_fn()
//    {
//        $options = get_option('plugin_options');
//        $items = array("Square", "Triangle", "Circle");
//        foreach ($items as $item) {
//            $checked = ($options['option_set1'] == $item) ? ' checked="checked" ' : '';
//            echo "<label><input " . $checked . " value='$item' name='plugin_options[option_set1]' type='radio' /> $item</label><br />";
//        }
//    }
//
//// WYSIWYG Visual Editor - Name: plugin_options[textarea_one]
//    public function setting_visual_fn()
//    {
//        $options = get_option('plugin_options');
//        $args = array("textarea_name" => "plugin_options[textarea_one]");
//        wp_editor($options['textarea_one'], "plugin_options[textarea_one]", $args);
//
//// Add another text box
//        $options = get_option('plugin_options');
//        $args = array("textarea_name" => "plugin_options[textarea_two]");
//        wp_editor($options['textarea_two'], "plugin_options[textarea_two]", $args);
//    }

    function admin_page_scripts($hook)
    {
        if ($hook === 'settings_page_' . $this->OPTION_SLUG) {
            wp_register_script('admin-settings-page', $this->config->PLUGIN_URL . 'assets/js/min/admin-settings-page.min.js', ['jquery']);
            wp_register_style('admin-settings-page', $this->config->PLUGIN_URL . 'assets/css/admin-settings-page.min.css');
            wp_enqueue_style('admin-settings-page');
            wp_enqueue_script('admin-settings-page');
        }
    }
}
