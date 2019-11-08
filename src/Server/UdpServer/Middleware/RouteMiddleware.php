<?php
namespace Imi\Server\UdpServer\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\UdpServer\PacketHandler;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean("UDPRouteMiddleware")
 */
class RouteMiddleware implements IMiddleware
{
    /**
     * @ServerInject("UdpRoute")
     *
     * @var \Imi\Server\UdpServer\Route\UdpRoute
     */
    protected $route;

    /**
     * @ServerInject("UdpRouteNotFoundHandler")
     *
     * @var \Imi\Server\UdpServer\Error\IUdpRouteNotFoundHandler
     */
    protected $notFoundHandler;

    /**
     * 处理方法
     *
     * @param IReceiveData $data
     * @param IReceiveHandler $handle
     * @return void
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        // 路由解析
        $result = $this->route->parse($data->getFormatData());
        if(null === $result || !is_callable($result->callable))
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