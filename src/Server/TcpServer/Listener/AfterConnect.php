<?php
namespace Imi\Server\TcpServer\Listener;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\ConnectEventParam;
use Imi\Server\Event\Listener\IConnectEventListener;

/**
 * Connect事件后置处理
 * @ClassEventListener(className="Imi\Server\TcpServer\Server",eventName="connect",priority=PHP_INT_MIN)
 */
class AfterConnect implements IConnectEventListener
{
	/**
	 * 默认的 WebSocket 握手
	 * @param ConnectEventParam $e
	 * @return void
	 */
	public function handle(ConnectEventParam $e)
	{
		// 释放请求上下文
		RequestContext::destroy();
	}

}