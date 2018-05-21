<?php
namespace Imi\Server\Http\Listener;

use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Event\Listener\IRequestEventListener;
use Imi\App;

/**
 * request事件前置处理
 * @ClassEventListener(className="Imi\Server\Http\Server",eventName="request",priority=PHP_INT_MAX)
 */
class BeforeRequest implements IRequestEventListener
{
	/**
	 * 事件处理方法
	 * @param RequestEventParam $e
	 * @return void
	 */
	public function handle(RequestEventParam $e)
	{
		// 上下文创建
		RequestContext::create();
		RequestContext::set('request', $e->request);
		RequestContext::set('response', $e->response);

		// 中间件
		$dispatcher = RequestContext::getBean('HttpDispatcher');
		$dispatcher->dispatch($e->request, $e->response);
	}
}