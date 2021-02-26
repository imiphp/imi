<?php

declare(strict_types=1);

namespace Imi\Event;

/**
 * 类事件管理器.
 */
class ClassEventManager
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
     * @param string $eventName
     * @param string $listenerClass
     * @param int    $priority
     *
     * @return void
     */
    public static function add(string $className, string $eventName, string $listenerClass, int $priority)
    {
        self::$map[$className][$eventName][$listenerClass] = [
            'priority'  => $priority,
        ];
    }

    /**
     * 获取对象事件定义.
     *
     * @param object $object
     * @param string $eventName
     *
     * @return array
     */
    public static function getByObjectEvent(object $object, string $eventName): array
    {
        $options = [];
        foreach (self::$map as $className => $option)
        {
            if (isset($option[$eventName]) && $object instanceof $className)
            {
                $options[] = $option[$eventName];
            }
        }

        return array_merge(...$options);
    }
}
