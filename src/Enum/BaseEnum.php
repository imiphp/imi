<?php

declare(strict_types=1);

namespace Imi\Enum;

abstract class BaseEnum
{
    /**
     * 根据值，获取常量名，失败返回null.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    public static function getName($value): ?string
    {
        $map = EnumManager::getKVMap(static::class);
        $key = array_search($value, $map);

        return $key ?? null;
    }

    /**
     * 获取值
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getValue(string $name)
    {
        return \constant(static::class . '::' . $name);
    }

    /**
     * 获取文本.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    public static function getText($value): ?string
    {
        $enumItem = EnumManager::getEnumItem(static::class, $value);
        if ($enumItem)
        {
            return $enumItem['text'];
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取配置.
     *
     * @param mixed $value
     *
     * @return array|null
     */
    public static function getData($value): ?array
    {
        return EnumManager::getEnumItem(static::class, $value);
    }

    /**
     * 获取所有名称.
     *
     * @return string[]
     */
    public static function getNames(): array
    {
        return EnumManager::getNames(static::class);
    }

    /**
     * 获取所有值
     *
     * @return array
     */
    public static function getValues(): array
    {
        return EnumManager::getValues(static::class);
    }

    /**
     * 获取键值对应数组.
     *
     * @return array
     */
    public static function getMap(): array
    {
        return EnumManager::getKVMap(static::class);
    }
}
