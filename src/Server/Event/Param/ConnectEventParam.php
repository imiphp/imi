<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class ConnectEventParam extends EventParam
{
	/**
	 * swoole 服务器对象
	 * @var \swoole_server
	 */
	public $server;

	/**
	 * 客户端连接的标识符
	 * @var int
	 */
	public $fd;

	/**
	 * Reactor线程ID
	 * @var int
	 */
	public $reactorID;
}