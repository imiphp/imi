<?php
namespace Imi\Server\Http;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Imi\Server\Http\Message\Response;
use Imi\RequestContext;

/**
 * @Bean("HttpDispatcher")
 */
class Dispatcher
{
	/**
	 * 中间件数组
	 * @var string[]
	 */
	protected $middlewares = [];

	public function dispatch($request)
	{
		$requestHandler = new RequestHandler($this->getMiddlewares());
		$response = $requestHandler->handle($request);
		$response->send();
	}

	protected function getMiddlewares()
	{
		return array_merge($this->middlewares, [

		]);
	}
}