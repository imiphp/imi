<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Bean\Annotation\Bean;

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

    public static function setMap(array $map): void
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
    public static function add(string $className, string $beanName, string $instanceType = Bean::INSTANCE_TYPE_SINGLETON): void
    {
        self::$map[$className]['class'] = self::$map[$beanName]['class'] = [
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
        return self::$map[$name]['class'] ?? null;
    }

    public static function addPropertyInject(string $name, string $propertyName, string $injectType, array $injectOptions): void
    {
        $options = [
            'injectType'    => $injectType,
            'injectOptions' => $injectOptions,
        ];
        $beanOption = self::$map[$name]['class'] ?? null;
        if ($beanOption)
        {
            self::$map[$beanOption['beanName']]['property'][$propertyName] = self::$map[$beanOption['className']]['property'][$propertyName] = $options;
        }
        else
        {
            self::$map[$name]['property'][$propertyName] = $options;
        }
    }

    public static function getPropertyInjects(string $name): array
    {
        return self::$map[$name]['property'] ?? [];
    }
}
