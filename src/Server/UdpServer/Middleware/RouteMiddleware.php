<?php
namespace Imi\Server\UdpServer\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\UdpServer\PacketHandler;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean
 */
class RouteMiddleware implements IMiddleware
{
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
        $route = RequestContext::getServerBean('UdpRoute');
        $result = $route->parse($data->getFormatData());
        if(null === $result || !is_callable($result->callable))
        {
            // 未找到匹配的命令，TODO:处理
            
        }
        else
        {
            RequestContext::set('routeResult', $result);
        }
        return $handler->handle($data);
    }

}