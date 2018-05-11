<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class WorkStartEventParam extends EventParam
{
	/**
	 * swoole 服务器对象
	 * @var \swoole_server
	 */
	public $server;

	/**
	 * Worker进程ID
	 * @var int
	 */
	public $workerID;
}