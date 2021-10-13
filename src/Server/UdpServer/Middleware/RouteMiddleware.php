<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\UdpServer\Error\IUdpRouteNotFoundHandler;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;
use Imi\Server\UdpServer\Route\UdpRoute;

/**
 * @Bean("UDPRouteMiddleware")
 */
class RouteMiddleware implements IMiddleware
{
    /**
     * @ServerInject("UdpRoute")
     */
    protected UdpRoute $route;

    /**
     * @ServerInject("UdpRouteNotFoundHandler")
     */
    protected IUdpRouteNotFoundHandler $notFoundHandler;

    /**
     * {@inheritDoc}
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        // 路由解析
        $result = $this->route->parse($data->getFormatData());
        if (null === $result || !\is_callable($result->callable))
        {
            // 未匹配到路由
            $result = $this->notFoundHandler->handle($data, $handler);
        }
        else
        {
            RequestContext::set('routeResult', $result);
            $result = $handler->handle($data);
        }

        return $result;
    }
}
