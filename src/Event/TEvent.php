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
     *
     * @var array
     */
    private array $eventChangeRecords = [];

    /**
     * 事件监听.
     *
     * @param string|string[] $name     事件名称
     * @param mixed           $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int             $priority 优先级，越大越先执行
     *
     * @return void
     */
    public function on($name, $callback, int $priority = 0)
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
            $this->eventChangeRecords[$eventName] = true;
        }
    }

    /**
     * 监听事件，仅触发一次
     *
     * @param string|string[] $name     事件名称
     * @param mixed           $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int             $priority 优先级，越大越先执行
     *
     * @return void
     */
    public function one($name, $callback, int $priority = 0)
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
            $this->eventChangeRecords[$eventName] = true;
        }
    }

    /**
     * 取消事件监听.
     *
     * @param string|string[] $name     事件名称
     * @param mixed|null      $callback 回调，支持回调函数、基于IEventListener的类名。为 null 则不限制
     *
     * @return void
     */
    public function off($name, $callback = null)
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
                    foreach ($events[$eventName] as $k => $item)
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
                $eventChangeRecords[$eventName] = true;
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
     *
     * @return void
     */
    public function trigger(string $name, array $data = [], ?object $target = null, string $paramClass = EventParam::class)
    {
        $eventQueue = &$this->eventQueue;
        // 获取回调列表
        if (!isset($eventQueue[$name]))
        {
            $options = ClassEventManager::getByObjectEvent($this, $name);
            if (!$options && empty($this->events[$name]))
            {
                return;
            }
            $eventsMap = &$this->events[$name];
            $queue = $this->rebuildEventQueue($name);
            foreach ($options as $option)
            {
                // 数据映射
                $eventsMap[] = $item = new EventItem(function ($param) use ($option) {
                    $obj = BeanFactory::newInstance($option['className']);
                    $obj->handle($param);
                }, $option['priority']);
                $queue->insert($item, $option['priority']);
            }
        }
        elseif (empty($this->events[$name]))
        {
            return;
        }
        elseif (isset($this->eventChangeRecords[$name]))
        {
            $queue = $this->rebuildEventQueue($name);
        }
        else
        {
            $queue = $eventQueue[$name];
        }
        $callbacks = clone $queue;
        // 实例化参数
        $param = new $paramClass($name, $data, $target);
        $oneTimeCallbacks = [];
        try
        {
            foreach ($callbacks as $option)
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
                $this->eventChangeRecords[$name] = true;
            }
        }
    }

    /**
     * 重建事件队列.
     *
     * @return \SplPriorityQueue
     */
    private function rebuildEventQueue(string $name): \SplPriorityQueue
    {
        $this->eventQueue[$name] = $queue = new \SplPriorityQueue();
        foreach ($this->events[$name] ?? [] as $item)
        {
            $queue->insert($item, $item->priority);
        }
        $this->eventChangeRecords[$name] = null;

        return $queue;
    }
}
