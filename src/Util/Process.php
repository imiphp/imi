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
        static $isTtySupported;

        if (null === $isTtySupported)
        {
            if ('/' === \DIRECTORY_SEPARATOR)
            {
                try
                {
                    $isTtySupported = (bool) @proc_open('echo 1 >/dev/null', [['file', '/dev/tty', 'r'], ['file', '/dev/tty', 'w'], ['file', '/dev/tty', 'w']], $pipes);
                }
                catch (\Throwable $th)
                {
                    $isTtySupported = false;
                }
            }
            else
            {
                $isTtySupported = false;
            }
        }

        return $isTtySupported;
    }
}
