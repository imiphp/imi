<?php

declare(strict_types=1);

namespace Imi\Event;

/**
 * 事件管理器.
 */
use Imi\App;
use Imi\Event\Contract\IEvent;

class EventManager
{
    use \Imi\Util\Traits\TStaticClass;

    private static array $map = [];

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map): void
    {
        self::$map = $map;
        foreach ($map as $eventName => $events)
        {
            foreach ($events as $listenerClass => $event)
            {
                if ($event['one'] ?? false)
                {
                    Event::one($eventName, static fn (IEvent $e) => App::newInstance($listenerClass)->handle($e), $event['priority']);
                }
                else
                {
                    Event::on($eventName, static fn (IEvent $e) => App::newInstance($listenerClass)->handle($e), $event['priority']);
                }
            }
        }
    }

    /**
     * 增加映射关系.
     */
    public static function add(string $eventName, string $listenerClass, int $priority, bool $one): void
    {
        self::$map[$eventName][$listenerClass] = [
            'priority'  => $priority,
            'one'       => $one,
        ];
    }
}
