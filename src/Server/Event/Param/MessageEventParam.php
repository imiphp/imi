<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class MessageEventParam extends EventParam
{
	/**
	 * websocket 服务器对象
	 * @var \swoole_websocket_server
	 */
	public $server;

	/**
	 * swoole 数据帧对象
	 * @var \swoole_websocket_frame
	 */
	public $frame;
}