<?php

declare(strict_types=1);

namespace Imi\Enum;

/**
 * 枚举管理器.
 */
class EnumManager
{
    private static array $map = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map): void
    {
        self::$map = $map;
    }

    /**
     * 增加映射关系.
     */
    public static function add(string $className, string $constName, array $options): void
    {
        $value = \constant($className . '::' . $constName);
        self::$map['map'][$className][$constName] = $value;
        self::$map['options'][$className][$value] = $options;
    }

    /**
     * 获得枚举项.
     *
     * @param mixed $value
     */
    public static function getEnumItem(string $className, $value): ?array
    {
        return self::$map['options'][$className][$value] ?? null;
    }

    /**
     * 获取常量名=>值集合.
     *
     * @return string[]
     */
    public static function getKVMap(string $className): array
    {
        return self::$map['map'][$className] ?? [];
    }

    /**
     * 获取所有名称.
     *
     * @return string[]
     */
    public static function getNames(string $className): array
    {
        return array_keys(self::$map['map'][$className] ?? []);
    }

    /**
     * 获取所有值
     */
    public static function getValues(string $className): array
    {
        $data = &self::$map;
        if (isset($data['options'][$className]))
        {
            return array_keys($data['options'][$className]);
        }
        else
        {
            return [];
        }
    }
}
