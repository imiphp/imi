<?php
namespace Imi\Task;

use Imi\Task\Interfaces\ITaskParam;
use Imi\Task\Interfaces\ITaskHandler;

class TaskInfo
{
	/**
	 * 任务执行器
	 * @var ITaskHandler
	 */
	private $taskHandler;
	
	/**
	 * 任务参数
	 * @var ITaskParam
	 */
	private $param;

	public function __construct(ITaskHandler $taskHandler, ITaskParam $param)
	{
		$this->taskHandler = $taskHandler;
		$this->param = $param;
	}

	/**
	 * Get the value of taskHandler
	 */ 
	public function getTaskHandler()
	{
		return $this->taskHandler;
	}

	/**
	 * Get the value of param
	 */ 
	public function getParam()
	{
		return $this->param;
	}
}