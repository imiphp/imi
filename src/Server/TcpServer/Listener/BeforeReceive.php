<?php
namespace Imi\Server\TcpServer\Listener;

use Imi\App;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\ReceiveEventParam;
use Imi\Server\Event\Listener\IReceiveEventListener;

/**
 * Receive事件前置处理
 * @ClassEventListener(className="Imi\Server\TcpServer\Server",eventName="receive",priority=PHP_INT_MAX)
 */
class BeforeReceive implements IReceiveEventListener
{
	/**
	 * 事件处理方法
	 * @param ReceiveEventParam $e
	 * @return void
	 */
	public function handle(ReceiveEventParam $e)
	{
		// 上下文创建
		RequestContext::create();
		RequestContext::set('fd', $e->frame->fd);
		RequestContext::set('server', $e->getTarget());

		// 中间件
		// $dispatcher = RequestContext::getServerBean('WebSocketDispatcher');
		// $dispatcher->dispatch(new Frame($e->frame));

	}
}