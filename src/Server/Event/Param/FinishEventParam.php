<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class FinishEventParam extends EventParam
{
	/**
	 * swoole 服务器对象
	 * @var \swoole_server
	 */
	public $server;

	/**
	 * 任务ID
	 * @var int
	 */
	public $taskID;

	/**
	 * 任务数据
	 * @var mixed
	 */
	public $data;
}