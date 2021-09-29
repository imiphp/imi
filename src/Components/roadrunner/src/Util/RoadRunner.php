<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Util;

class RoadRunner
{
    private function __construct()
    {
    }

    /**
     * 获取 RoadRunner 二进制文件路径.
     */
    public static function getBinaryPath(): ?string
    {
        $path = getenv('IMI_ROADRUNNER_BINARY');
        if (false !== $path && '' !== $path && is_file($path))
        {
            return $path;
        }
        $path = (new \Symfony\Component\Process\ExecutableFinder())->find('rr');
        if (null !== $path)
        {
            return $path;
        }

        return null;
    }
}
