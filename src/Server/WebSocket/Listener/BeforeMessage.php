<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\App;
use Imi\Worker;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Server\WebSocket\Message\Frame;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\MessageEventParam;
use Imi\Server\Event\Listener\IMessageEventListener;

/**
 * Message事件前置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="message",priority=PHP_INT_MAX)
 */
class BeforeMessage implements IMessageEventListener
{
	/**
	 * 事件处理方法
	 * @param MessageEventParam $e
	 * @return void
	 */
	public function handle(MessageEventParam $e)
	{
		if(!Worker::isInited())
		{
			$GLOBALS['WORKER_START_END_RESUME_COIDS'][] = Coroutine::getuid();
			Coroutine::suspend();
		}
		// 上下文创建
		RequestContext::create();
		RequestContext::set('fd', $e->frame->fd);
		RequestContext::set('server', $e->getTarget());

		// 中间件
		$dispatcher = RequestContext::getServerBean('WebSocketDispatcher');
		$dispatcher->dispatch(new Frame($e->frame));

	}
}