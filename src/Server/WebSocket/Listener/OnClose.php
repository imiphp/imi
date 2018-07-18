<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\ServerManage;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;

/**
 * Close事件前置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="close")
 */
class OnClose implements ICloseEventListener
{
	/**
	 * 事件处理方法
	 * @param CloseEventParam $e
	 * @return void
	 */
	public function handle(CloseEventParam $e)
	{
		RequestContext::create();
		RequestContext::set('server', $e->getTarget());
		
		// 当前连接离开所有组
		$e->getTarget()->getBean('FdMap')->leaveAll($e->fd);

		RequestContext::destroy();
		ConnectContext::destroy($e->fd);
	}
}