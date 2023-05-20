<?php

declare(strict_types=1);

namespace Imi\Util;

class Process
{
    use \Imi\Util\Traits\TStaticClass;

    public static function isTtySupported(): bool
    {
        return '/' === \DIRECTORY_SEPARATOR && stream_isatty(\STDOUT);
    }
}
