<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 位操作工具类.
 */
class Bit
{
    private function __construct()
    {
    }

    /**
     * 判断是否包含值
     */
    public static function has(int $value, int $subValue): bool
    {
        return $value === $subValue || ($subValue === ($value & $subValue));
    }
}
