<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Service\SettingService;
use WPR\Twig\TwigInitEnvironment;

class SettingsView extends \WP_List_Table
{
    private $twig;

    /**
     * @var SettingService
     */
    private $serviceSetting;

    public function __construct(SettingService $serviceSetting)
    {
        parent::__construct();
        $this->twig = TwigInitEnvironment::getTwigEnvironment();
        $this->serviceSetting = $serviceSetting;

        $this->changeHiddenMenu();
    }

    public function addOptionsPage()
    {
        echo $this->twig->render('admin/settings.twig', [
            'options' => $this->serviceSetting->getSetting(),
            'formHiddenField' => $this->formHiddenFields(),
            'formSubmitButton' => $this->formSubmitButton(),
        ]);
    }

    /**
     * Custom activate menu.
     */
    public function changeHiddenMenu()
    {
        if (isset($_GET['page']) and $_GET['page'] == 'wpr-settings') {
            add_action('admin_init', function () {
                global $submenu, $menu;

                foreach ($submenu['options-general.php'] as $key => $value) {
                    if ('wp-post-rating' == $value[2]) {
                        $submenu['options-general.php'][$key][4] = 'current';
                    }
                }

                foreach ($menu as $key => $value) {
                    if ('options-general.php' == $value[2]) {
                        $menu[$key][4] .= ' wp-menu-open';
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
        echo '</select>';
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
            echo '<input '.$checked." id='{$option_name}_{$key}' name='{$option_name}[]' type='checkbox' value='{$key}' />";
            echo '</label>';
        }
    }

    public function get_columns()
    {
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

    /**
     * @return false|string
     *                      Hidden fields for setting form
     */
    private function formHiddenFields()
    {
        ob_start();
        settings_fields('wpr_options_group');
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * @return false|string
     *                      Submit button for form
     */
    private function formSubmitButton()
    {
        ob_start();
        submit_button();
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
