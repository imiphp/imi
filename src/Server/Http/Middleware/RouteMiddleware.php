<?php

declare(strict_types=1);

namespace Imi\Server\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Http\Error\IHttpNotFoundHandler;
use Imi\Server\Http\Route\HttpRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean(name="RouteMiddleware", recursion=false)
 */
class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @ServerInject("HttpRoute")
     */
    protected HttpRoute $route;

    /**
     * @ServerInject("HttpNotFoundHandler")
     */
    protected IHttpNotFoundHandler $notFoundHandler;

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RequestContext::getContext();
        // 路由解析
        // @phpstan-ignore-next-line
        $result = $this->route->parse($request);
        if (null === $result)
        {
            // 未匹配到路由
            // @phpstan-ignore-next-line
            $response = $this->notFoundHandler->handle($handler, $request, $context['response']);
            $context['response'] = $response;
        }
        else
        {
            $context['routeResult'] = $result;
            $response = $handler->handle($request);
        }

        return $response;
    }
}
