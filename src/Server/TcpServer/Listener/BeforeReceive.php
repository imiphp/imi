<?php
namespace Imi\Server\TcpServer\Listener;

use Imi\App;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\ReceiveEventParam;
use Imi\Server\Event\Listener\IReceiveEventListener;
use Imi\Server\TcpServer\Message\ReceiveData;

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
		RequestContext::set('fd', $e->fd);
		RequestContext::set('server', $e->getTarget());

		// 中间件
		$dispatcher = RequestContext::getServerBean('TcpDispatcher');
		$dispatcher->dispatch(new ReceiveData($e->fd, $e->reactorID, $e->data));

	}
}