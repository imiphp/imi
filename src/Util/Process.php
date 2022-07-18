<?php

declare(strict_types=1);

namespace Imi\Util;

class Process
{
    private function __construct()
    {
    }

    public static function isTtySupported(): bool
    {
        return '/' === \DIRECTORY_SEPARATOR && stream_isatty(\STDOUT);
    }
}
