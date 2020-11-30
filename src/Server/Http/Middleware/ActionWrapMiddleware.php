<?php

declare(strict_types=1);

namespace Imi\Server\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("ActionWrapMiddleware")
 */
class ActionWrapMiddleware implements MiddlewareInterface
{
    /**
     * 动作中间件.
     *
     * @var string
     */
    protected string $actionMiddleware = ActionMiddleware::class;

    /**
     * 处理方法.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 获取路由结果
        /** @var \Imi\Server\Http\Route\RouteResult $result */
        $result = RequestContext::get('routeResult');
        if (null === $result)
        {
            return $handler->handle($request);
        }
        $middlewares = $result->routeItem->middlewares;
        $middlewares[] = $this->actionMiddleware;
        $handler = new RequestHandler($middlewares);

        return $handler->handle($request);
    }
}
