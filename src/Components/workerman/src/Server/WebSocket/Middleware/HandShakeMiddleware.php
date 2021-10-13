<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean(name="HandShakeMiddleware", env="workerman")
 */
class HandShakeMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var IHttpResponse $response */
        $response = $handler->handle($request);
        $requestContext = RequestContext::getContext();
        /** @var \Imi\Server\Http\Route\RouteResult $routeResult */
        $routeResult = $requestContext['routeResult'] ?? null;
        if (isset($routeResult->routeItem->wsConfig->parserClass))
        {
            ConnectionContext::set('dataParser', $routeResult->routeItem->wsConfig->parserClass);
        }

        return $response;
    }
}
