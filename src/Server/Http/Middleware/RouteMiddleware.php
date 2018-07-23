<?php
namespace Imi\Server\Http\Middleware;

use Imi\App;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Stream\MemoryStream;
use Imi\Controller\HttpController;
use Imi\Server\Http\RequestHandler;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\View\Parser\ViewParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Imi\Server\Http\Middleware\ActionMiddleware;

/**
 * @Bean
 */
class RouteMiddleware implements MiddlewareInterface
{
	/**
	 * 处理方法
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 * @return ResponseInterface
	 */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// 获取Response对象
		$response = $handler->handle($request);
		RequestContext::set('response', $response);
		// 路由解析
		$route = RequestContext::getServerBean('HttpRoute');
		$result = $route->parse($request);
		if(null === $result || !is_callable($result['callable']))
		{
			// 未匹配到路由
			return $response->withStatus(404);
		}
		else
		{
			RequestContext::set('routeResult', $result);

			$middlewares = $result['middlewares'];
			$middlewares[] = ActionMiddleware::class;
			$requestHandler = new RequestHandler($middlewares);
			return $requestHandler->handle($request);
		}
	}

}