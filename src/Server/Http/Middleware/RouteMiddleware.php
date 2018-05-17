<?php
namespace Imi\Server\Http\Middleware;

use Imi\App;
use Imi\Util\Call;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Stream\MemoryStream;
use Imi\Controller\HttpController;
use Imi\Server\Http\Message\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
		$response = $handler->handle($request);
		$route = $request->getServerInstance()->getBean('HttpRoute');
		$result = $route->parse($request);
		if(null === $result)
		{
			return $response->withStatus(404);
		}
		else
		{
			$isObject = isset($result['callable'][0]) && $result['callable'][0] instanceof HttpController;
			if($isObject)
			{
				$result['callable'][0] = clone $result['callable'][0];
				$result['callable'][0]->request = $request;
				$result['callable'][0]->response = $response;
			}
			$actionResult = Call::callUserFuncArray($result['callable'], $this->prepareActionParams($request, $result));
			if($isObject)
			{
				$response = $result['callable'][0]->response;
			}
			$response->getBody()->write($actionResult);
		}
		return $response;
	}
	
	/**
	 * 准备调用action的参数
	 * @param Request $e
	 * @param array $routeResult
	 * @return array
	 */
	private function prepareActionParams(Request $request, $routeResult)
	{
		try{
			if(is_array($routeResult['callable']))
			{
				$ref = new \ReflectionMethod($routeResult['callable'][0], $routeResult['callable'][1]);
			}
			else if(!$routeResult['callable'] instanceof \Closure)
			{
				$ref = new \ReflectionFunction($routeResult['callable']);
			}
			else
			{
				return [];
			}
		}
		catch(\Throwable $ex)
		{
			return [];
		}
		$result = [];
		foreach($ref->getParameters() as $param)
		{
			if(isset($routeResult['params'][$param->name]))
			{
				$result[] = $routeResult['params'][$param->name];
			}
			else if($request->hasPost($param->name))
			{
				$result[] = $request->post($param->name);
			}
			else if($request->hasGet($param->name))
			{
				$result[] = $request->get($param->name);
			}
			else if($param->isOptional())
			{
				$result[] = $param->getDefaultValue();
			}
			else
			{
				$result[] = null;
			}
		}
		return $result;
	}
}