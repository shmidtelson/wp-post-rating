<?php

declare(strict_types=1);

/**
 * PSR-4 autoloader for plugin classes (no Composer vendor at runtime).
 */
spl_autoload_register(
    static function (string $class): void {
        $prefix = 'WPR\\';
        $baseDir = dirname(__DIR__) . '/src/';

        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_readable($file)) {
            require $file;
        }
    }
);
