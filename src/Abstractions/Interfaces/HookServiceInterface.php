<?php

declare(strict_types=1);

namespace WPR\Abstractions\Interfaces;

interface HookServiceInterface
{
    public function hooks(): void;
}