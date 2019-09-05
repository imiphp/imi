<?php
namespace Imi\Server\Http\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("ActionWrapMiddleware")
 */
class ActionWrapMiddleware implements MiddlewareInterface
{
    /**
     * 处理方法
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 获取路由结果
        $result = RequestContext::get('routeResult');
        if(null === $result)
        {
            return $handler->handle($request);
        }
        $middlewares = $result->routeItem->middlewares;
        $middlewares[] = ActionMiddleware::class;
        $handler = new RequestHandler($middlewares);
        return $handler->handle($request);
    }

}