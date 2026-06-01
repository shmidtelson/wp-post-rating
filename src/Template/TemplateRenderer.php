<?php

declare(strict_types=1);

namespace WPR\Template;

use RuntimeException;

/**
 * Renders PHP templates from the templates/ directory.
 */
final class TemplateRenderer
{
    public function __construct(
        private readonly string $templatesPath,
    ) {
    }

    /**
     * @param array<string, mixed> $args Variables available in the template.
     */
    public function render(string $template, array $args = []): string
    {
        $file = $this->templatesPath . ltrim($template, '/') . '.php';

        if (! is_readable($file)) {
            throw new RuntimeException(sprintf('Template not found: %s', $template));
        }

        ob_start();
        extract($args, EXTR_SKIP);
        include $file;

        return (string) ob_get_clean();
    }
}
