<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\App;
use Imi\AppContexts;
use Imi\Event\Contract\IEvent;
use Imi\Util\Traits\TSingleton;

class Event
{
    use TEvent
    {
        on as public __on;
        one as public __one;
        off as public __off;
        trigger as public __trigger;
        dispatch as public __dispatch;
    }

    use TSingleton;

    public function __construct(?string $eventDispatcher = null, ?string $listenerProvider = null)
    {
        $this->eventDispatcher = $eventDispatcher ?? App::get(AppContexts::EVENT_DISPATCHER, EventDispatcher::class);
        $this->listenerProvider = $listenerProvider ?? App::get(AppContexts::EVENT_LISTENER_PROVIDER, ListenerProvider::class);
    }

    /**
     * 事件监听.
     *
     * @param string|string[] $eventNames 事件名称
     * @param int             $priority   优先级，越大越先执行
     */
    public static function on(string|array $eventNames, callable $listener, int $priority = 0): void
    {
        self::getInstance()->__on($eventNames, $listener, $priority);
    }

    /**
     * 监听事件，仅触发一次
     *
     * @param string|string[] $eventNames 事件名称
     * @param int             $priority   优先级，越大越先执行
     */
    public static function one(string|array $eventNames, callable $listener, int $priority = 0): void
    {
        self::getInstance()->__one($eventNames, $listener, $priority);
    }

    /**
     * 取消事件监听.
     *
     * @param string|string[] $eventNames 事件名称
     * @param callable|null   $listener   回调，支持回调函数、基于IEventListener的类名。为 null 则不限制
     */
    public static function off(string|array $eventNames, ?callable $listener = null): void
    {
        self::getInstance()->__off($eventNames, $listener);
    }

    /**
     * 触发事件.
     *
     * @deprecated 3.1
     *
     * @param array       $data       数据
     * @param object|null $target     目标对象
     * @param string      $paramClass 参数类
     */
    public static function trigger(string $eventName, array $data = [], ?object $target = null, string $paramClass = EventParam::class): void
    {
        self::getInstance()->__trigger($eventName, $data, $target, $paramClass);
    }

    /**
     * 触发事件.
     */
    public static function dispatch(IEvent $event = null, ?string $eventName = null, ?object $target = null): void
    {
        self::getInstance()->__dispatch($event, $eventName, $target);
    }
}
