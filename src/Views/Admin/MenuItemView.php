<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Template\TemplateRenderer;

class MenuItemView
{
    public function __construct(
        private readonly TemplateRenderer $templates,
    ) {
    }

    /**
     * @param array<int, string> $links
     * @return array<int, string>
     */
    public function addSettingsLinkToPluginList(array $links): array
    {
        array_unshift($links, $this->templates->render('admin/parts/item-menu'));

        return $links;
    }
}
