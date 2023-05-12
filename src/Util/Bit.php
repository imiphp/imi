<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 位操作工具类.
 */
class Bit
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 判断是否包含值
     */
    public static function has(int $value, int $subValue): bool
    {
        return $value === $subValue || ($subValue === ($value & $subValue));
    }
}
