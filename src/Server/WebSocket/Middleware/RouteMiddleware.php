<?php
namespace Imi\Server\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\IMessageHandler;

/**
 * @Bean
 */
class RouteMiddleware implements IMiddleware
{
	/**
	 * 处理方法
	 *
	 * @param IFrame $request
	 * @param IMessageHandler $handler
	 * @return void
	 */
    public function process(IFrame $request, IMessageHandler $handler)
	{
		return date('Y-m-d H:i:s');
	}

}