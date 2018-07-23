<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class MessageEventParam extends EventParam
{
	/**
	 * 服务器对象
	 * @var \Imi\Server\Base
	 */
	public $server;

	/**
	 * swoole 数据帧对象
	 * @var \swoole_websocket_frame
	 */
	public $frame;
}