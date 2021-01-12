<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 字符串工具类.
 */
class Text
{
    private function __construct()
    {
    }

    /**
     * 字符串是否以另一个字符串开头.
     *
     * @param string $string
     * @param string $compare
     * @param bool   $caseSensitive
     *
     * @return bool
     */
    public static function startwith(string $string, string $compare, bool $caseSensitive = true): bool
    {
        if ($caseSensitive)
        {
            return 0 === strpos($string, $compare);
        }
        else
        {
            return 0 === stripos($string, $compare);
        }
    }

    /**
     * 字符串是否以另一个字符串结尾.
     *
     * @param string $string
     * @param string $compare
     * @param bool   $caseSensitive
     *
     * @return bool
     */
    public static function endwith(string $string, string $compare, bool $caseSensitive = true): bool
    {
        if ($caseSensitive)
        {
            return $compare === strrchr($string, $compare);
        }
        else
        {
            return substr_compare($compare, strrchr($string, $compare), 0, null, true);
        }
    }

    /**
     * 插入字符串.
     *
     * @param string $string       原字符串
     * @param int    $position     位置
     * @param string $insertString 被插入的字符串
     *
     * @return string
     */
    public static function insert(string $string, int $position, string $insertString): string
    {
        return substr_replace($string, $insertString, $position, 0);
    }

    /**
     * 字符串是否为空字符串或者为null.
     *
     * @param string|null $string
     *
     * @return bool
     */
    public static function isEmpty(?string $string): bool
    {
        return '' === $string || null === $string;
    }

    /**
     * 转为驼峰命名，会把下划线后字母转为大写.
     *
     * @param string $name
     *
     * @return string
     */
    public static function toCamelName(string $name): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
    }

    /**
     * 转为每个单词大写的命名，会把下划线后字母转为大写.
     *
     * @param string $name
     *
     * @return string
     */
    public static function toPascalName(string $name): string
    {
        return ucfirst(static::toCamelName($name));
    }

    /**
     * 转为下划线命名.
     *
     * @param string $name
     * @param bool   $toLower
     *
     * @return string
     */
    public static function toUnderScoreCase(string $name, bool $toLower = true): string
    {
        $result = trim(preg_replace('/[A-Z]/', '_\0', $name), '_');
        if ($toLower)
        {
            $result = strtolower($result);
        }

        return $result;
    }
}
