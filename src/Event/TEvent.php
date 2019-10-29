<?php
namespace Imi\Event;

use Imi\Bean\Parser\ClassEventParser;
use Imi\Bean\BeanFactory;

trait TEvent
{
    /**
     * 事件数据映射原始数据
     * @var \Imi\Event\EventItem[][]
     */
    private $events = [];

    /**
     * 事件队列，按执行顺序排
     * @var \SplPriorityQueue[]
     */
    private $eventQueue = [];

    /**
     * 事件更改记录
     *
     * @var array
     */
    private $eventChangeRecords = [];

    /**
     * 事件监听
     * @param string $name 事件名称
     * @param mixed $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int $priority 优先级，越大越先执行
     * @return void
     */
    public function on($name, $callback, $priority = 0)
    {
        $this->events[$name][] = new EventItem($callback, $priority);
        $this->eventChangeRecords[$name] = true;
    }

    /**
     * 监听事件，仅触发一次
     * @param string $name 事件名称
     * @param mixed $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int $priority 优先级，越大越先执行
     * @return void
     */
    public function one($name, $callback, $priority = 0)
    {
        $this->events[$name][] = new EventItem($callback, $priority, true);
        $this->eventChangeRecords[$name] = true;
    }

    /**
     * 取消事件监听
     * @param string $name 事件名称
     * @param mixed $callback 回调，支持回调函数、基于IEventListener的类名
     * @return void
     */
    public function off($name, $callback)
    {
        if(isset($this->events[$name]))
        {
            $map = &$this->events[$name];
            // 数据映射
            foreach($this->events[$name] as $k => $item)
            {
                if($callback === $item->callback)
                {
                    unset($map[$k]);
                }
            }
            $this->eventChangeRecords[$name] = true;
        }
    }

    /**
     * 触发事件
     * @param string $name 事件名称
     * @param array $data 数据
     * @param mixed $target 目标对象
     * @param string $paramClass 参数类
     * @return void
     */
    public function trigger($name, $data = [], $target = null, $paramClass = EventParam::class)
    {
        // 获取回调列表
        if(!isset($this->eventQueue[$name]))
        {
            $classEventdata = ClassEventParser::getInstance()->getData();
            if(empty($classEventdata) && empty($this->events[$name]))
            {
                return;
            }
            $eventsMap = &$this->events[$name];
            $this->rebuildEventQueue($name);
            foreach($classEventdata as $className => $option)
            {
                if($this instanceof $className && isset($option[$name]))
                {
                    foreach($option[$name] as $callback)
                    {
                        // 数据映射
                        $eventsMap[] = $item = new EventItem($callback['className'], $callback['priority']);
                        $this->eventQueue[$name]->insert($item, $callback['priority']);
                    }
                }
            }
        }
        else if(empty($this->events[$name]))
        {
            return;
        }
        else if(isset($this->eventChangeRecords[$name]))
        {
            $this->rebuildEventQueue($name);
        }
        $callbacks = clone $this->eventQueue[$name];
        // 实例化参数
        $param = new $paramClass($name, $data, $target);
        $oneTimeCallbacks = [];
        foreach($callbacks as $option)
        {
            $callback = $option->callback;
            // 回调类型处理，优先判断为类的情况
            $type = 'callback';
            if(is_string($callback) && class_exists($callback))
            {
                $type = 'class';
            }
            // 回调执行
            switch($type)
            {
                case 'callback':
                    $callback($param);
                    break;
                case 'class':
                    $obj = BeanFactory::newInstance($callback);
                    $obj->handle($param);
                    break;
            }
            // 仅触发一次
            if($option->oneTime)
            {
                $oneTimeCallbacks[] = $option;
            }
            // 阻止事件传播
            if($param->isPropagationStopped())
            {
                break;
            }
        }
        // 仅触发一次的处理
        if(isset($oneTimeCallbacks[0]))
        {
            $eventsMap = &$this->events[$name];
            foreach($eventsMap as $eventsKey => $item)
            {
                foreach($oneTimeCallbacks as $oneTimeCallbacksKey => $oneTimeItem)
                {
                    if($oneTimeItem === $item)
                    {
                        unset($eventsMap[$eventsKey], $oneTimeCallbacks[$oneTimeCallbacksKey]);
                        break;
                    }
                }
            }
            $this->eventChangeRecords[$name] = true;
        }
    }

    /**
     * 重建事件队列
     * @return void
     */
    private function rebuildEventQueue($name)
    {
        $this->eventQueue[$name] = new \SplPriorityQueue;
        foreach($this->events[$name] ?? [] as $item)
        {
            $this->eventQueue[$name]->insert($item, $item->priority);
        }
        $this->eventChangeRecords[$name] = null;
    }
}