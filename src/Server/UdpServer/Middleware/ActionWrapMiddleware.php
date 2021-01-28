<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;
use Imi\Server\UdpServer\PacketHandler;

/**
 * @Bean("UDPActionWrapMiddleware")
 */
class ActionWrapMiddleware implements IMiddleware
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
     * @param IPacketData    $data
     * @param IPacketHandler $handler
     *
     * @return void
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        // 获取路由结果
        $result = RequestContext::get('routeResult');
        if (null === $result)
        {
            return $handler->handle($data);
        }
        $middlewares = $result->routeItem->middlewares;
        $middlewares[] = $this->actionMiddleware;
        $handler = new PacketHandler($middlewares);

        return $handler->handle($data);
    }
}
