<?php
declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Views\AbstractView;

class MenuItemView extends AbstractView
{
    public function addSettingsLinkToPluginList($links)
    {
        array_push($links, $this->twig->render('admin/parts/item-menu.twig'));
        return $links;
    }
}