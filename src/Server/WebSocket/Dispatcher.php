<?php
namespace Imi\Server\WebSocket;

use Imi\App;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * @Bean("WebSocketDispatcher")
 */
class Dispatcher
{
	/**
	 * 中间件数组
	 * @var string[]
	 */
	protected $middlewares = [];

	public function dispatch(IFrame $frame)
	{
		$requestHandler = new MessageHandler($this->getMiddlewares());
		$responseData = $requestHandler->handle($frame);
		if(null !== $responseData)
		{
			RequestContext::getServer()->getSwooleServer()->push($frame->getFd(), $responseData);
		}
	}

	protected function getMiddlewares()
	{
		return array_merge($this->middlewares, [

		]);
	}
}