<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class TaskEventParam extends EventParam
{
	/**
	 * 服务器对象
	 * @var \Imi\Server\Base
	 */
	public $server;

	/**
	 * 任务ID
	 * @var int
	 */
	public $taskID;

	/**
	 * worker进程ID
	 * @var int
	 */
	public $workerID;

	/**
	 * 任务数据
	 * @var mixed
	 */
	public $data;
}