<?php
namespace Imi\Event;

trait TEvent
{
	private $events = [];

	/**
	 * 事件监听
	 * @param string $name 事件名称
	 * @param mixed $callback 回调，支持回调函数、基于IEventListener的类名
	 * @return void
	 */
	public function on($name, $callback)
	{
		if(!isset($this->events[$name]))
		{
			$this->events[$name] = [];
		}
		$this->events[$name][] = [
			'callback'	=>	$callback
		];
	}

	/**
	 * 监听事件，仅触发一次
	 * @param string $name 事件名称
	 * @param mixed $callback 回调，支持回调函数、基于IEventListener的类名
	 * @return void
	 */
	public function one($name, $callback)
	{
		if(!isset($this->events[$name]))
		{
			$this->events[$name] = [];
		}
		$this->events[$name][] = [
			'callback'	=>	$callback,
			'one'		=>	true,
		];
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
			$length = count($this->events[$name]);
			for($i = 0; $i < $length; ++$i)
			{
				if($callback === $this->events[$name][$i]['callback'])
				{
					unset($this->events[$name][$i]);
				}
			}
		}
	}

	/**
	 * 触发事件
	 * @param string $name 事件名称
	 * @param array $data 数据
	 * @param mixed $target 目标对象
	 * @return void
	 */
	public function trigger($name, $data = [], $target = null)
	{
		$param = new EventParam($name, $target, $data);
		if(isset($this->events[$name]))
		{
			foreach($this->events[$name] as $i => $option)
			{
				// 仅触发一次的处理
				if(isset($option['one']) && $option['one'])
				{
					unset($this->events[$name][$i]);
				}
				// 回调类型处理，优先判断为类的情况
				$type = 'callback';
				if(is_string($option['callback']) && class_exists($option['callback']))
				{
					$type = 'class';
				}
				// 回调执行
				switch($type)
				{
					case 'callback':
						call_user_func_array($option['callback'], [$param]);
						break;
					case 'class':
						$obj = new $option['callback'];
						call_user_func_array([$obj, 'handle'], [$param]);
						break;
				}
				// 阻止事件传播
				if($param->isStopPropagation())
				{
					return;
				}
			}
		}
	}
}