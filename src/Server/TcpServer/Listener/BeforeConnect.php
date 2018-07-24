<?php
namespace Imi\Server\TcpServer\Listener;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\ConnectEventParam;
use Imi\Server\Event\Listener\IConnectEventListener;

/**
 * Connect事件前置处理
 * @ClassEventListener(className="Imi\Server\TcpServer\Server",eventName="connect",priority=PHP_INT_MAX)
 */
class BeforeConnect implements IConnectEventListener
{
	/**
	 * 默认的 WebSocket 握手
	 * @param ConnectEventParam $e
	 * @return void
	 */
	public function handle(ConnectEventParam $e)
	{
		// 上下文创建
		RequestContext::create();
		RequestContext::set('server', $e->server);
		RequestContext::set('fd', $e->fd);

		// 连接上下文创建
		ConnectContext::create();
	}

}