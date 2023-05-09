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
        return random_int($min, $max);
    }

    /**
     * 随机小数.
     *
     * @param int $precision 最大小数位数
     */
    public static function float(float $min = \PHP_INT_MIN, float $max = \PHP_INT_MAX, ?int $precision = null): float
    {
        $result = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        if (null !== $precision)
        {
            return round($result, $precision);
        }

        return $result;
    }

    /**
     * 随机生成小数文本.
     *
     * @param int $precision 最大小数位数
     */
    public static function number(float $min = \PHP_INT_MIN, float $max = \PHP_INT_MAX, int $precision = 2): string
    {
        return Digital::scientificToNum((string) self::float($min, $max, $precision), $precision);
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
     * 随机生成字节集.
     */
    public static function bytes(string $bytes, int $min, ?int $max = null): string
    {
        $length = mt_rand($min, $max ?? $min);
        $charLength = \strlen($bytes);
        $result = str_repeat(' ', $length);
        for ($i = 0; $i < $length; ++$i)
        {
            $result[$i] = $bytes[mt_rand(1, $charLength) - 1];
        }

        return $result;
    }

    /**
     * 随机生成字母.
     */
    public static function letter(int $min, ?int $max = null): string
    {
        return static::bytes('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $min, $max ?? $min);
    }

    /**
     * 随机生成数字.
     */
    public static function digital(int $min, ?int $max = null): string
    {
        return static::bytes('0123456789', $min, $max ?? $min);
    }

    /**
     * 随机生成字母和数字.
     */
    public static function letterAndNumber(int $min, ?int $max = null): string
    {
        return static::bytes('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $min, $max ?? $min);
    }
}
