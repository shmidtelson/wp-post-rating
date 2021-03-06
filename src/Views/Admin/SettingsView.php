<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Service\SettingService;
use WPR\Service\TwigEnvironmentService;

class SettingsView
{
    /**
     * @var TwigEnvironmentService
     */
    private $twigService;

    /**
     * @var SettingService
     */
    private $settingService;

    public function __construct(
        TwigEnvironmentService $twigService,
        SettingService $settingService
    ) {
        $this->twigService = $twigService;
        $this->settingService = $settingService;
    }

    public function addOptionsPage()
    {
        $this->changeHiddenMenu();

        echo $this->twigService->getTwig()->render('admin/settings.twig', [
            'options' => $this->settingService->getSetting(),
            'formHiddenField' => $this->formHiddenFields(),
            'formSubmitButton' => $this->formSubmitButton(),
            'formActionLink' => esc_url(admin_url('admin-post.php')),
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

    public function get_columns()
    {
    }

    /**
     * @return false|string
     *                      Hidden fields for setting form
     */
    private function formHiddenFields()
    {
        ob_start();
        wp_nonce_field('wpr-update');
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
