<?php
namespace Imi\Server\Http\Listener;

use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Event\Listener\IRequestEventListener;

/**
 * request事件后置处理
 * @ClassEventListener(className="Imi\Server\Http\Server",eventName="request",priority=PHP_INT_MIN)
 */
class AfterRequest implements IRequestEventListener
{
	/**
	 * 事件处理方法
	 * @param RequestEventParam $e
	 * @return void
	 */
	public function handle(RequestEventParam $e)
	{
		RequestContext::destroy();
	}
}