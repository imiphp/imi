<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\TcpServer\Error\ITcpRouteNotFoundHandler;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;
use Imi\Server\TcpServer\Route\TcpRoute;

/**
 * @Bean(name="TCPRouteMiddleware", recursion=false)
 */
class RouteMiddleware implements IMiddleware
{
    /**
     * @ServerInject("TcpRoute")
     */
    protected TcpRoute $route;

    /**
     * @ServerInject("TcpRouteNotFoundHandler")
     */
    protected ITcpRouteNotFoundHandler $notFoundHandler;

    /**
     * {@inheritDoc}
     */
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        // 路由解析
        $result = $this->route->parse($data->getFormatData());
        if (null === $result || !\is_callable($result->callable))
        {
            // 未匹配到路由
            return $this->notFoundHandler->handle($data, $handler);
        }
        else
        {
            RequestContext::set('routeResult', $result);

            return $handler->handle($data);
        }
    }
}
