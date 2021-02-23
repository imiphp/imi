<?php

declare(strict_types=1);

namespace Imi\Bean;

/**
 * Bean 管理器.
 */
class BeanManager
{
    private static array $map = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map)
    {
        self::$map = $map;
    }

    /**
     * 增加映射关系.
     *
     * @param string $className
     * @param string $beanName
     * @param string $instanceType
     *
     * @return void
     */
    public static function add(string $className, string $beanName, string $instanceType)
    {
        self::$map[$className] = self::$map[$beanName] = [
            'beanName'     => $beanName,
            'className'    => $className,
            'instanceType' => $instanceType,
        ];
    }

    /**
     * 获取.
     *
     * @param string $name
     *
     * @return array|null
     */
    public static function get(string $name): ?array
    {
        return self::$map[$name] ?? null;
    }
}
