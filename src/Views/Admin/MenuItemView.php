<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Service\TwigEnvironmentService;

class MenuItemView
{
    /**
     * @var TwigEnvironmentService
     */
    private $twigService;

    public function __construct(TwigEnvironmentService $twigService)
    {
        $this->twigService = $twigService;
    }

    /**
     * @param $links
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return mixed
     */
    public function addSettingsLinkToPluginList($links)
    {
        array_unshift($links, $this->twigService->getTwig()->render('admin/parts/item-menu.twig'));

        return $links;
    }
}
