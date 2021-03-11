<?php

namespace Imi\Event;

use Imi\Bean\BeanFactory;
use Imi\Bean\Parser\ClassEventParser;

trait TEvent
{
    /**
     * 事件数据映射原始数据.
     *
     * @var \Imi\Event\EventItem[][]
     */
    private $events = [];

    /**
     * 事件队列，按执行顺序排.
     *
     * @var \SplPriorityQueue[]
     */
    private $eventQueue = [];

    /**
     * 事件更改记录.
     *
     * @var array
     */
    private $eventChangeRecords = [];

    /**
     * 事件监听.
     *
     * @param string|string[] $name     事件名称
     * @param mixed           $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int             $priority 优先级，越大越先执行
     *
     * @return void
     */
    public function on($name, $callback, $priority = 0)
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
    public function one($name, $callback, $priority = 0)
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
     * @param string $name       事件名称
     * @param array  $data       数据
     * @param mixed  $target     目标对象
     * @param string $paramClass 参数类
     *
     * @return void
     */
    public function trigger($name, $data = [], $target = null, $paramClass = EventParam::class)
    {
        $eventQueue = &$this->eventQueue;
        // 获取回调列表
        if (!isset($eventQueue[$name]))
        {
            $classEventdata = ClassEventParser::getInstance()->getData();
            if (empty($classEventdata) && empty($this->events[$name]))
            {
                return;
            }
            $eventsMap = &$this->events[$name];
            $this->rebuildEventQueue($name);
            foreach ($classEventdata as $className => $option)
            {
                if (isset($option[$name]) && $this instanceof $className)
                {
                    foreach ($option[$name] as $callback)
                    {
                        // 数据映射
                        $eventsMap[] = $item = new EventItem(function ($param) use ($callback) {
                            $obj = BeanFactory::newInstance($callback['className']);
                            $obj->handle($param);
                        }, $callback['priority']);
                        $eventQueue[$name]->insert($item, $callback['priority']);
                    }
                }
            }
        }
        elseif (empty($this->events[$name]))
        {
            return;
        }
        elseif (isset($this->eventChangeRecords[$name]))
        {
            $this->rebuildEventQueue($name);
        }
        $callbacks = clone $eventQueue[$name];
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
     * @param string $name
     *
     * @return void
     */
    private function rebuildEventQueue($name)
    {
        $this->eventQueue[$name] = $queue = new \SplPriorityQueue();
        foreach ($this->events[$name] ?? [] as $item)
        {
            $queue->insert($item, $item->priority);
        }
        $this->eventChangeRecords[$name] = null;
    }
}
