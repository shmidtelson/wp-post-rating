<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Service\SettingService;
use WPR\Template\TemplateRenderer;

class SettingsView
{
    public function __construct(
        private readonly TemplateRenderer $templates,
        private readonly SettingService $settingService,
    ) {
    }

    public function addOptionsPage(): void
    {
        $this->changeHiddenMenu();

        echo $this->templates->render('admin/settings', [
            'options' => $this->settingService->getSetting(),
            'formHiddenField' => $this->formHiddenFields(),
            'formSubmitButton' => $this->formSubmitButton(),
            'formActionLink' => esc_url(admin_url('admin-post.php')),
        ]);
    }

    public function changeHiddenMenu(): void
    {
        if (isset($_GET['page']) && $_GET['page'] === 'wpr-settings') {
            add_action('admin_init', function (): void {
                global $submenu, $menu;

                foreach ($submenu['options-general.php'] as $key => $value) {
                    if ('wp-post-rating' === $value[2]) {
                        $submenu['options-general.php'][$key][4] = 'current';
                    }
                }

                foreach ($menu as $key => $value) {
                    if ('options-general.php' === $value[2]) {
                        $menu[$key][4] .= ' wp-menu-open';
                    }
                }
            });
        }
    }

    private function formHiddenFields(): string
    {
        ob_start();
        wp_nonce_field('wpr-update');
        $html = ob_get_contents();
        ob_end_clean();

        return (string) $html;
    }

    private function formSubmitButton(): string
    {
        ob_start();
        submit_button();
        $html = ob_get_contents();
        ob_end_clean();

        return (string) $html;
    }
}
