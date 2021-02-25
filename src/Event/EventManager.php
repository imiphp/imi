<?php

declare(strict_types=1);

namespace Imi\Event;

/**
 * 事件管理器.
 */
class EventManager
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
        foreach ($map as $eventName => $events)
        {
            foreach ($events as $listenerClass => $event)
            {
                Event::on($eventName, $listenerClass, $event['priority']);
            }
        }
    }

    /**
     * 增加映射关系.
     *
     * @param string $eventName
     * @param string $listenerClass
     * @param int    $priority
     *
     * @return void
     */
    public static function add(string $eventName, string $listenerClass, int $priority)
    {
        self::$map[$eventName][$listenerClass] = [
            'priority'  => $priority,
        ];
    }
}
