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
     */
    protected string $actionMiddleware = ActionMiddleware::class;

    /**
     * 处理方法.
     *
     * @return mixed
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
        if ($middlewares)
        {
            $middlewares[] = $this->actionMiddleware;
            $subHandler = new PacketHandler($middlewares);

            return $subHandler->handle($data);
        }
        else
        {
            /** @var \Imi\Server\UdpServer\Middleware\IMiddleware $requestHandler */
            $requestHandler = RequestContext::getServerBean($this->actionMiddleware);

            return $requestHandler->process($data, $handler);
        }
    }
}
