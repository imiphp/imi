<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\Bean\BeanFactory;

trait TEvent
{
    /**
     * 事件数据映射原始数据.
     *
     * @var \Imi\Event\EventItem[][]
     */
    private array $events = [];

    /**
     * 事件队列，按执行顺序排.
     *
     * @var \SplPriorityQueue[]
     */
    private array $eventQueue = [];

    /**
     * 事件更改记录.
     */
    private array $eventChangeRecords = [];

    /**
     * 排序后的事件对象
     *
     * @var \Imi\Event\EventItem[][]
     */
    private array $sortedEventQueue = [];

    /**
     * 事件监听.
     *
     * @param string|string[] $name     事件名称
     * @param mixed           $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int             $priority 优先级，越大越先执行
     */
    public function on($name, $callback, int $priority = 0): void
    {
        foreach ((array) $name as $eventName)
        {
            if (\is_string($callback) && class_exists($callback))
            {
                $callbackClass = $callback;
                $callback = function ($param) use ($callback) {
                    $obj = BeanFactory::newInstance($callback);
                    $obj->handle($param);
                };
            }
            else
            {
                $callbackClass = null;
            }
            $this->events[$eventName][] = $item = new EventItem($callback, $priority);
            if (null !== $callbackClass)
            {
                $item->callbackClass = $callbackClass;
            }
            if (isset($this->eventQueue[$eventName]))
            {
                $this->eventQueue[$eventName]->insert($item, $priority);
                $this->eventChangeRecords[$eventName] ??= 2;
            }
        }
    }

    /**
     * 监听事件，仅触发一次
     *
     * @param string|string[] $name     事件名称
     * @param mixed           $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int             $priority 优先级，越大越先执行
     */
    public function one($name, $callback, int $priority = 0): void
    {
        foreach ((array) $name as $eventName)
        {
            if (\is_string($callback) && class_exists($callback))
            {
                $callbackClass = $callback;
                $callback = function ($param) use ($callback) {
                    $obj = BeanFactory::newInstance($callback);
                    $obj->handle($param);
                };
            }
            else
            {
                $callbackClass = null;
            }
            $this->events[$eventName][] = $item = new EventItem($callback, $priority, true);
            if (null !== $callbackClass)
            {
                $item->callbackClass = $callbackClass;
            }
            if (isset($this->eventQueue[$eventName]))
            {
                $this->eventQueue[$eventName]->insert($item, $priority);
                $this->eventChangeRecords[$eventName] ??= 2;
            }
        }
    }

    /**
     * 取消事件监听.
     *
     * @param string|string[] $name     事件名称
     * @param mixed|null      $callback 回调，支持回调函数、基于IEventListener的类名。为 null 则不限制
     */
    public function off($name, $callback = null): void
    {
        $events = &$this->events;
        $eventChangeRecords = &$this->eventChangeRecords;
        foreach ((array) $name as $eventName)
        {
            if (isset($events[$eventName]))
            {
                if ($callback)
                {
                    $map = &$events[$eventName];
                    // 数据映射
                    foreach ($map as $k => $item)
                    {
                        if ($callback === $item->callback || $callback === $item->callbackClass)
                        {
                            unset($map[$k]);
                        }
                    }
                }
                else
                {
                    unset($events[$eventName]);
                }
                $eventChangeRecords[$eventName] = 1;
            }
        }
    }

    /**
     * 触发事件.
     *
     * @param string      $name       事件名称
     * @param array       $data       数据
     * @param object|null $target     目标对象
     * @param string      $paramClass 参数类
     */
    public function trigger(string $name, array $data = [], ?object $target = null, string $paramClass = EventParam::class): void
    {
        // 获取回调列表
        if (!isset($this->eventQueue[$name]))
        {
            $options = ClassEventManager::getByObjectEvent($this, $name);
            if (!$options && empty($this->events[$name]))
            {
                return;
            }
            $eventsMap = &$this->events[$name];
            if ($options)
            {
                foreach ($options as $className => $option)
                {
                    // 数据映射
                    $this->on($name, $className, $option['priority']);
                }
            }
            $this->rebuildEventQueue($name);
        }
        elseif (empty($this->events[$name]))
        {
            return;
        }
        elseif (isset($this->eventChangeRecords[$name]))
        {
            $this->rebuildEventQueue($name);
        }
        // 实例化参数
        $param = new $paramClass($name, $data, $target);
        $oneTimeCallbacks = [];
        try
        {
            /** @var EventItem $option */
            foreach ($this->sortedEventQueue[$name] as $option)
            {
                // 仅触发一次
                if ($option->oneTime)
                {
                    $oneTimeCallbacks[] = $option;
                }
                // 回调执行
                ($option->callback)($param);
                // 阻止事件传播
                if ($param->isPropagationStopped())
                {
                    break;
                }
            }
        }
        finally
        {
            // 仅触发一次的处理
            if (isset($oneTimeCallbacks[0]))
            {
                $eventsMap = &$this->events[$name];
                foreach ($eventsMap as $eventsKey => $item)
                {
                    foreach ($oneTimeCallbacks as $oneTimeCallbacksKey => $oneTimeItem)
                    {
                        if ($oneTimeItem === $item)
                        {
                            unset($eventsMap[$eventsKey], $oneTimeCallbacks[$oneTimeCallbacksKey]);
                            break;
                        }
                    }
                }
                $this->eventChangeRecords[$name] = 1;
            }
        }
    }

    /**
     * 重建事件队列.
     */
    private function rebuildEventQueue(string $name): void
    {
        if (1 === ($this->eventChangeRecords[$name] ?? 1))
        {
            $this->eventQueue[$name] = $queue = new \SplPriorityQueue();
            $events = $this->events[$name] ?? [];
            if ($events)
            {
                foreach ($events as $item)
                {
                    $queue->insert($item, $item->priority);
                }
            }
            $clonedQueue = clone $queue;
        }
        else
        {
            $clonedQueue = clone $this->eventQueue[$name];
        }
        $this->sortedEventQueue[$name] = iterator_to_array($clonedQueue);
        $this->eventChangeRecords[$name] = null;
    }
}
