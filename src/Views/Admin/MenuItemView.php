<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Abstractions\Abstracts\AbstractView;

class MenuItemView extends AbstractView
{
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
        array_push($links, $this->twig->getTwig()->render('admin/parts/item-menu.twig'));

        return $links;
    }
}
