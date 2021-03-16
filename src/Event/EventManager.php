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

    public static function setMap(array $map): void
    {
        foreach (self::$map as $eventName => $events)
        {
            foreach ($events as $listenerClass => $event)
            {
                Event::off($eventName, $listenerClass);
            }
        }
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
     */
    public static function add(string $eventName, string $listenerClass, int $priority): void
    {
        self::$map[$eventName][$listenerClass] = [
            'priority'  => $priority,
        ];
    }
}
