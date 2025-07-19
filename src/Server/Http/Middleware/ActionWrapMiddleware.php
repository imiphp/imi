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
use Imi\Server\Http\Middleware\ActionMiddleware;
use Imi\Server\Http\Middleware\RouteMiddleware;
use Imi\Server\Annotation\ServerInject;

/**
 * @Bean("ActionWrapMiddleware")
 */
class ActionWrapMiddleware implements MiddlewareInterface
{

    /**
     * @ServerInject("RouteMiddleware")
     */
    protected RouteMiddleware $routeMiddleware;

    /**
     * @ServerInject("ActionMiddleware")
     */
    protected ActionMiddleware $actionMiddleware;

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        // 获取路由结果
        /** @var \Imi\Server\Http\Route\RouteResult|null $result */
        $result = RequestContext::get('routeResult');
        if ($result) {
            $middlewares = $result->routeItem->middlewares;
            if ($middlewares) {
                $middlewares[] = ActionMiddleware::class;
                $subHandler = new RequestHandler($middlewares);
                $response = $subHandler->handle($request);
                if ($response) {
                    return $response;
                }
            }
        }

        $context = RequestContext::getContext();
        /** @var \Imi\Server\Http\Message\Response $response */
        $response = $context['response'] ?? null;
        if (!$response) {
            throw new \RuntimeException('ResponseContent not found ');
        }

        $result = $this->routeMiddleware->dispatch($request, $response);
        if ($result) {
            /** @var Response $response */
            $response = $result;
        } elseif ($result = $this->actionMiddleware->dispatch($request, $response)) {
            /** @var Response $response */
            $response = $result;
        } else {
            /** @var \Psr\Http\Server\MiddlewareInterface $requestHandler */
            $requestHandler = RequestContext::getServerBean('ActionMiddleware');
            if (!$requestHandler) {
                throw new \RuntimeException('RequestContent not found ActionMiddleware');
            }
            $response =  $requestHandler->process($request, $handler);
        }
        return $response;
    }
}
