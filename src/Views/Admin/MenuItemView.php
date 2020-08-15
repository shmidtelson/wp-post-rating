<?php
declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Views\AbstractView;

class MenuItemView extends AbstractView
{
    /**
     * @param $links
     *
     * @return mixed
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function addSettingsLinkToPluginList($links)
    {
        array_push($links, $this->twig->render('admin/parts/item-menu.twig'));
        return $links;
    }
}
