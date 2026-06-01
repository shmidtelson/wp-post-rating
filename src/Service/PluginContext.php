<?php

declare(strict_types=1);

namespace WPR\Service;

/**
 * Immutable plugin paths and metadata set at bootstrap.
 */
final class PluginContext
{
    public function __construct(
        public readonly string $file,
        public readonly string $path,
        public readonly string $url,
        public readonly string $version,
        public readonly string $basename,
    ) {
    }
}
