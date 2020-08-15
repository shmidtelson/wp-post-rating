<?php

declare(strict_types=1);

namespace WPR\Twig;

use DI\Container;
use Twig\TwigFunction;
use WPR\Service\ConfigService;
use Twig\Extension\AbstractExtension;

class TransExtension extends AbstractExtension
{
    private $config;

    public function __construct()
    {
        $this->config = (new Container())->get(ConfigService::class);
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('__', [$this, 'translateData']),
        ];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function translateData(string $text)
    {
        return __($text, ConfigService::PLUGIN_NAME);
    }
}
