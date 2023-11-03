<?php

declare(strict_types=1);

namespace Imi\Enum;

abstract class BaseEnum
{
    /**
     * 根据值，获取常量名，失败返回null.
     */
    public static function getName(mixed $value): ?string
    {
        $map = EnumManager::getKVMap(static::class);
        $key = array_search($value, $map);

        return false === $key ? null : $key;
    }

    /**
     * 获取值
     *
     * @return mixed
     */
    public static function getValue(string $name)
    {
        return \constant(static::class . '::' . $name);
    }

    /**
     * 获取文本.
     */
    public static function getText(mixed $value): ?string
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
     */
    public static function getData(mixed $value): ?array
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
     */
    public static function getValues(): array
    {
        return EnumManager::getValues(static::class);
    }

    /**
     * 获取键值对应数组.
     */
    public static function getMap(): array
    {
        return EnumManager::getKVMap(static::class);
    }

    /**
     * 验证值是否合法.
     */
    public static function validate(mixed $value): bool
    {
        return \in_array($value, static::getValues());
    }

    /**
     * 验证值断言.
     *
     * 值不合法会抛出异常
     *
     * @throws \InvalidArgumentException
     */
    public static function assert(mixed $value): void
    {
        if (!static::validate($value))
        {
            throw new \InvalidArgumentException(sprintf('Invalid value %s in enum %s', $value, static::class));
        }
    }
}
