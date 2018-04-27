<?php
namespace Imi\Event;

class EventParam
{
	/**
	 * 事件名称
	 * @var string
	 */
	protected $eventName;

	/**
	 * 触发该事件的对象
	 * @var object
	 */
	protected $target;

	/**
	 * 数据
	 * @var array
	 */
	protected $data = [];

	/**
	 * 阻止事件继续传播
	 * @var boolean
	 */
	protected $stopPropagation = false;

	public function __construct($eventName, $data = [], $target = null)
	{
		$this->eventName = $eventName;
		$this->target = $target;
		$this->data = $data;
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}
	}

	/**
	 * 获取事件名称
	 * @return string
	 */
	public function getEventName()
	{
		return $this->eventName;
	}

	/**
	 * 获取触发该事件的对象
	 * @return object
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * 获取数据
	 * @return data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * 阻止事件继续传播
	 * @param boolean $isStop 是否阻止事件继续传播
	 */
	public function stopPropagation($isStop = true)
	{
		$this->stopPropagation = $isStop;
	}

	/**
	 * 是否阻止事件继续传播
	 * @return boolean
	 */
	public function isPropagationStopped()
	{
		return $this->stopPropagation;
	}
}