<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\App;
use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\MessageEventParam;
use Imi\Server\Event\Listener\IMessageEventListener;

/**
 * Message事件后置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="message",priority=PHP_INT_MIN)
 */
class AfterMessage implements IMessageEventListener
{
	/**
	 * 事件处理方法
	 * @param MessageEventParam $e
	 * @return void
	 */
	public function handle(MessageEventParam $e)
	{
		// 释放请求的进程池资源
		PoolManager::destroyCurrentContext();
		// 销毁请求上下文
		RequestContext::destroy();
	}
}