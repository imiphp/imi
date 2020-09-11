<?php

namespace Imi\Util;

/**
 * 位操作工具类.
 */
abstract class Bit
{
    /**
     * 判断是否包含值
     *
     * @param int $value
     * @param int $subValue
     *
     * @return bool
     */
    public static function has($value, $subValue)
    {
        return $value === $subValue || ($subValue === ($value & $subValue));
    }
}
