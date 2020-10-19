<?php

namespace Imi\Util;

/**
 * 数字相关工具类.
 */
abstract class Digital
{
    /**
     * 科学计数转小数形式的.
     *
     * @param string $num       科学计数法字符串  如 2.1E-5
     * @param int    $precision 小数点保留位数 默认5位
     *
     * @return string
     */
    public static function scientificToNum($num, $precision = 5)
    {
        if (false !== stripos($num, 'e'))
        {
            $a = explode('e', strtolower($num));

            return bcmul($a[0], bcpow(10, $a[1], $precision), $precision);
        }

        return $num;
    }
}
