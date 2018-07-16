<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;
use Imi\ConnectContext;

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
		ConnectContext::destroy($e->fd);
	}
}