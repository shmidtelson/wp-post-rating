<?php

declare(strict_types=1);

namespace WPR\Service;

use UnexpectedValueException;
use WPR\Entity\SettingEntity;
use WPR\Template\TemplateRenderer;

class SettingFormService
{
    const SUCCESS_KEY = 'wpr-success';

    public function __construct(
        private readonly TemplateRenderer $templates,
        private readonly SettingService $settingService,
    ) {
    }

    public function saveForm(): void
    {
        $this->validateNonce();

        $settingEntity = $this->settingService->getSetting();
        $settingEntity->setStarsMainColor($this->sanitizeColorField('main_color'));
        $settingEntity->setStarsTextColor($this->sanitizeColorField('text_color'));
        $settingEntity->setStarsTextBackgroundColor($this->sanitizeColorField('text_background_color'));
        $settingEntity->setSchemaEnable(array_key_exists('schema_enable', $_POST));
        $settingEntity->setPosition($this->sanitizePositionField());
        $this->validate($settingEntity);

        $this->settingService->save($settingEntity);

        $referer = isset($_POST['_wp_http_referer'])
            ? esc_url_raw(wp_unslash($_POST['_wp_http_referer']))
            : admin_url('options-general.php?page=' . ConfigService::OPTIONS_KEY);
        $location = add_query_arg([self::SUCCESS_KEY => 'ID'], $referer);
        wp_safe_redirect($location);
        exit;
    }

    private function validateNonce(): void
    {
        $nonce = isset($_POST['_wpnonce']) ? wp_unslash($_POST['_wpnonce']) : '';
        $action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';

        if ($nonce && $action && wp_verify_nonce($nonce, $action)) {
            return;
        }

        throw new UnexpectedValueException('Nonce validation error');
    }

    private function sanitizePositionField(): string
    {
        $position = isset($_POST['position'])
            ? sanitize_text_field(wp_unslash($_POST['position']))
            : ConfigService::POSITION_SHORTCODE;

        if (! in_array($position, ConfigService::getAllowedPositions(), true)) {
            return ConfigService::POSITION_SHORTCODE;
        }

        return $position;
    }

    private function sanitizeColorField(string $field): string
    {
        if (! isset($_POST[$field])) {
            return '';
        }

        $color = sanitize_hex_color(wp_unslash($_POST[$field]));

        return $color !== null ? $color : '';
    }

    private function validate(SettingEntity $settingEntity): void
    {
        $this->validateColorHEX($settingEntity->getStarsMainColor());
        $this->validateColorHEX($settingEntity->getStarsTextColor());
        $this->validateColorHEX($settingEntity->getStarsTextBackgroundColor());
    }

    private function validateColorHEX(string $value): bool
    {
        $items = explode('#', $value);

        if (ctype_xdigit($items[1]) && strlen($value) <= 7) {
            return true;
        }

        throw new UnexpectedValueException('Hex color validation error');
    }

    public function successMessage(): void
    {
        if (! isset($_GET[self::SUCCESS_KEY])) {
            return;
        }

        echo $this->templates->render(
            'admin/messages/success',
            ['content' => __('Settings saved successful', ConfigService::PLUGIN_NAME)]
        );
    }
}
