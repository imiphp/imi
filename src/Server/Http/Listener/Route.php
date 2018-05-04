<?php
namespace Imi\Server\Http\Listener;

use Imi\Bean\Annotation\Parser;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Event\Listener\IRequestEventListener;

/**
 * http服务器路由
 * @ClassEventListener(className="Imi\Server\Http\Server",eventName="request")
 */
class Route implements IRequestEventListener
{
	/**
	 * 事件处理方法
	 * @param RequestEventParam $e
	 * @return void
	 */
	public function handle(RequestEventParam $e)
	{
		// echo 'request:', PHP_EOL;
		// $e->response->write(time());
		$e->response->end('hello');
	}
}