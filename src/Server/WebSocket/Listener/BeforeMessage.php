<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\App;
use Imi\RequestContext;
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
		// 上下文创建
		RequestContext::create();
		// RequestContext::set('request', $e->request);
		// RequestContext::set('response', $e->response);

		// 中间件
		// $dispatcher = RequestContext::getBean('HttpDispatcher');
		// $dispatcher->dispatch($e->request, $e->response);
		var_dump($e->frame->data);
	}
}