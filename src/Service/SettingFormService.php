<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Abstractions\Traits\GetSettingsServiceTrait;
use WPR\Entity\SettingEntity;

class SettingFormService
{
    use GetSettingsServiceTrait;

    const SUCCESS_KEY = 'wpr-success';

    private $twig;

    public function __construct(TwigEnvironmentService $twigService)
    {
        $this->twig = $twigService;
    }

    public function saveForm(): void
    {
        $this->validateNonce();

        $settingEntity = $this->getSettings()->getSetting();
        $settingEntity->setStarsMainColor(htmlspecialchars($_POST['main_color']));
        $settingEntity->setStarsSecondColor(htmlspecialchars($_POST['second_color']));
        $settingEntity->setStarsTextColor(htmlspecialchars($_POST['text_color']));
        $settingEntity->setStarsTextBackgroundColor(htmlspecialchars($_POST['text_background_color']));
        $settingEntity->setSchemaEnable(array_key_exists('schema_enable', $_POST));
        $this->validate($settingEntity);

        $this->getSettings()->save($settingEntity);

        // TODO: Вызывает баг. Сохранение остается
        $location = add_query_arg([self::SUCCESS_KEY => 'ID'], htmlspecialchars($_POST['_wp_http_referer']));
        wp_redirect($location);
    }

    /**
     * @return bool
     */
    private function validateNonce()
    {
        if (wp_verify_nonce(htmlspecialchars($_POST['_wpnonce']), $_POST['action'])) {
            return true;
        }

        throw new \UnexpectedValueException('Nonce validation error');
    }

    private function validate(SettingEntity $settingEntity)
    {
        $this->validateColorHEX($settingEntity->getStarsMainColor());
        $this->validateColorHEX($settingEntity->getStarsSecondColor());
        $this->validateColorHEX($settingEntity->getStarsTextColor());
        $this->validateColorHEX($settingEntity->getStarsTextBackgroundColor());
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function validateColorHEX(string $value)
    {
        $items = explode('#', $value);

        if (ctype_xdigit($items[1]) && strlen($value) <= 7) {
            return true;
        }

        throw new \UnexpectedValueException('Hex color validation error');
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function successMessage()
    {
        if (!isset($_GET[self::SUCCESS_KEY])) {
            return;
        }

        echo $this->twig->getTwig()->render(
            'admin/messages/success.twig',
            ['content' => __('Settings saved successful')]
        );
    }
}
