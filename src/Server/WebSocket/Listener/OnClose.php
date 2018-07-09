<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;

/**
 * Message事件前置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="close",priority=PHP_INT_MAX)
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
		var_dump('close');
	}
}