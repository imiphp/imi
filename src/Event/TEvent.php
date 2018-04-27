<?php
namespace Imi\Event;

use Imi\Util\KVStorage;

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
	 * 事件监听
	 * @param string $name 事件名称
	 * @param mixed $callback 回调，支持回调函数、基于IEventListener的类名
	 * @param int $priority 优先级，越大越先执行，相等时后监听后执行
	 * @return void
	 */
	public function on($name, $callback, $priority = 0)
	{
		if(!isset($this->events[$name]))
		{
			$this->events[$name] = new KVStorage;
		}
		if(!isset($this->eventQueue[$name]))
		{
			$this->eventQueue[$name] = new \SplPriorityQueue;
		}
		// 数据映射
		$this->events[$name]->attach($callback, [
			'callback'	=>	$callback,
			'priority'	=>	$priority,
		]);
		// 事件队列
		$this->eventQueue[$name]->insert($callback, $priority);
	}

	/**
	 * 监听事件，仅触发一次
	 * @param string $name 事件名称
	 * @param mixed $callback 回调，支持回调函数、基于IEventListener的类名
	 * @param int $priority 优先级，越大越先执行，相等时后监听后执行
	 * @return void
	 */
	public function one($name, $callback, $priority = 0)
	{
		if(!isset($this->events[$name]))
		{
			$this->events[$name] = new KVStorage;
		}
		if(!isset($this->eventQueue[$name]))
		{
			$this->eventQueue[$name] = new \SplPriorityQueue;
		}
		// 数据映射
		$this->events[$name]->attach($callback, [
			'callback'	=>	$callback,
			'priority'	=>	$priority,
			'one'		=>	true,
		]);
		// 事件队列
		$this->eventQueue[$name]->insert($callback, $priority);
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
			// 重建事件队列
			$this->rebuildEventQueue($name);
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
		if(!isset($this->eventQueue[$name]))
		{
			return;
		}
		$callbacks = clone $this->eventQueue[$name];
		$param = new $paramClass($name, $data, $target);
		$hasOne = false;
		foreach($callbacks as $callback)
		{
			// 事件配置
			$option = $this->events[$name]->offsetGet($callback);
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
					$obj = new $callback;
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
				return;
			}
		}
		// 仅触发一次的处理
		if($hasOne)
		{
			$this->rebuildEventQueue($name);
		}
	}

	/**
	 * 重建事件队列
	 * @return void
	 */
	private function rebuildEventQueue($name)
	{
		$this->eventQueue[$name] = new \SplPriorityQueue;
		foreach($this->events[$name] as $event)
		{
			$this->eventQueue[$name]->insert($event['callback'], $event['priority']);
		}
	}
}