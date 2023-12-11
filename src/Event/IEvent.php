<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\Event\Contract\IEvent as ContractIEvent;

interface IEvent
{
    /**
     * 事件监听.
     *
     * @param string|string[] $eventNames 事件名称
     * @param int             $priority   优先级，越大越先执行
     */
    public function on(string|array $eventNames, callable $listener, int $priority = 0): void;

    /**
     * 监听事件，仅触发一次
     *
     * @param string|string[] $eventNames 事件名称
     * @param int             $priority   优先级，越大越先执行
     */
    public function one(string|array $eventNames, callable $listener, int $priority = 0): void;

    /**
     * 取消事件监听.
     *
     * @param string|string[] $eventNames 事件名称
     * @param callable|null   $listener   回调，支持回调函数、基于IEventListener的类名。为 null 则不限制
     */
    public function off(string|array $eventNames, ?callable $listener = null): void;

    /**
     * 触发事件.
     *
     * @deprecated 3.1
     *
     * @param array       $data       数据
     * @param object|null $target     目标对象
     * @param string      $paramClass 参数类
     */
    public function trigger(string $eventName, array $data = [], ?object $target = null, string $paramClass = EventParam::class): void;

    /**
     * 事件调度.
     */
    public function dispatch(?ContractIEvent $event = null, ?string $eventName = null, ?object $target = null): void;
}
