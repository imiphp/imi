<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 随机生成一些东西的工具类.
 */
abstract class Random
{
    /**
     * 随机整数.
     */
    public static function int(int $min = \PHP_INT_MIN, int $max = \PHP_INT_MAX): int
    {
        return mt_rand($min, $max);
    }

    /**
     * 随机生成小数.
     *
     * @param int $precision 最大小数位数
     */
    public static function number(float $min = \PHP_INT_MIN, float $max = \PHP_INT_MAX, int $precision = 2): string
    {
        $value = round($min + mt_rand() / mt_getrandmax() * ($max - $min), $precision);

        return Digital::scientificToNum((string) $value, $precision);
    }

    /**
     * 随机生成文本.
     */
    public static function text(string $chars, int $min, ?int $max = null): string
    {
        $length = mt_rand($min, $max ?? $min);
        $charLength = mb_strlen($chars);
        $result = '';
        for ($i = 0; $i < $length; ++$i)
        {
            $result .= mb_substr($chars, mt_rand(1, $charLength) - 1, 1);
        }

        return $result;
    }

    /**
     * 随机生成字母.
     */
    public static function letter(int $min, ?int $max = null): string
    {
        return static::text('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $min, $max ?? $min);
    }

    /**
     * 随机生成数字.
     */
    public static function digital(int $min, ?int $max = null): string
    {
        return static::text('0123456789', $min, $max ?? $min);
    }

    /**
     * 随机生成字母和数字.
     */
    public static function letterAndNumber(int $min, ?int $max = null): string
    {
        return static::text('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $min, $max ?? $min);
    }
}
