<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\WebSocket\Error\IWSRouteNotFoundHandler;
use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\Route\WSRoute;

/**
 * @Bean("WebSocketRouteMiddleware")
 */
class RouteMiddleware implements IMiddleware
{
    /**
     * @ServerInject("WSRoute")
     */
    protected WSRoute $route;

    /**
     * @ServerInject("WSRouteNotFoundHandler")
     */
    protected IWSRouteNotFoundHandler $notFoundHandler;

    /**
     * 处理方法.
     *
     * @return mixed
     */
    public function process(IFrame $frame, IMessageHandler $handler)
    {
        // 路由解析
        $result = $this->route->parse($frame->getFormatData());
        if (null === $result || !\is_callable($result->callable))
        {
            // 未匹配到路由
            $result = $this->notFoundHandler->handle($frame, $handler);
        }
        else
        {
            RequestContext::set('routeResult', $result);
            $result = $handler->handle($frame);
        }

        return $result;
    }
}
