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
class ActionWrapMiddleware implements IMiddleware
{
    /**
     * 处理方法
     *
     * @param IPacketData $data
     * @param IPacketHandler $handler
     * @return void
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        // 获取路由结果
        $result = RequestContext::get('routeResult');
        if(null === $result)
        {
            return $handler->handle($data);
        }
        $middlewares = $result->routeItem->middlewares;
        $middlewares[] = ActionMiddleware::class;
        $handler = new PacketHandler($middlewares);
        return $handler->handle($data);
    }

}