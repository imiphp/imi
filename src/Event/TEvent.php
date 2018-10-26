<?php
namespace Imi\Event;

use Imi\Util\KVStorage;
use Imi\Bean\Parser\ClassEventParser;
use Imi\Bean\BeanFactory;

trait TEvent
{
    /**
     * 事件数据映射原始数据
     * @var KVStorage[]
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
        if(!isset($this->events[$name]))
        {
            $this->events[$name] = new KVStorage;
        }
        // 数据映射
        $this->events[$name]->attach($callback, [
            'callback' => $callback,
            'priority' => $priority,
        ]);
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
        if(!isset($this->events[$name]))
        {
            $this->events[$name] = new KVStorage;
        }
        // 数据映射
        $this->events[$name]->attach($callback, [
            'callback'  => $callback,
            'priority'  => $priority,
            'one'       => true,
        ]);
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
            // 数据映射
            $this->events[$name]->detach($callback);
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
        // ClassEventListener支持
        $callbacks = $this->getTriggerCallbacks($name);
        // 实例化参数
        $param = new $paramClass($name, $data, $target);
        $hasOne = false;
        foreach($callbacks as $callback)
        {
            // 事件配置
            if(isset($this->events[$name]))
            {
                $option = $this->events[$name]->offsetGet($callback);
            }
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
                    call_user_func_array($callback, [$param]);
                    break;
                case 'class':
                    $obj = BeanFactory::newInstance($callback);
                    call_user_func_array([$obj, 'handle'], [$param]);
                    break;
            }
            // 仅触发一次
            if(isset($option['one']) && $option['one'])
            {
                $this->events[$name]->detach($callback);
                $hasOne = true;
            }
            // 阻止事件传播
            if($param->isPropagationStopped())
            {
                break;
            }
        }
        // 仅触发一次的处理
        if($hasOne)
        {
            $this->eventChangeRecords[$name] = true;
        }
    }

    /**
     * 获取事件触发回调列表
     *
     * @param string $name
     * @return array
     */
    private function getTriggerCallbacks($name)
    {
        if(!isset($this->events[$name]))
        {
            $this->events[$name] = new KVStorage;
        }
        if(!isset($this->eventQueue[$name]))
        {
            $this->rebuildEventQueue($name);
            $data = ClassEventParser::getInstance()->getData();
            foreach($data as $className => $option)
            {
                if($this instanceof $className && isset($option[$name]))
                {
                    foreach($option[$name] as $callback)
                    {
                        // 数据映射
                        $this->events[$name]->attach($callback['className'], [
                            'callback' => $callback['className'],
                            'priority' => $callback['priority'],
                        ]);
                        $this->eventQueue[$name]->insert($callback['className'], $callback['priority']);
                    }
                }
            }
        }
        else if(isset($this->eventChangeRecords[$name]))
        {
            $this->rebuildEventQueue($name);
        }
        $callbacks = clone $this->eventQueue[$name];
        return $callbacks;
    }

    /**
     * 重建事件队列
     * @return void
     */
    private function rebuildEventQueue($name)
    {
        $this->eventQueue[$name] = new \SplPriorityQueue;
        foreach($this->events[$name] as $object)
        {
            $event = $this->events[$name][$object];
            $this->eventQueue[$name]->insert($event['callback'], $event['priority']);
        }
        $this->eventChangeRecords[$name] = null;
    }
}