<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Error;

use Imi\Swoole\Server\UdpServer\IPacketHandler;
use Imi\Swoole\Server\UdpServer\Message\IPacketData;

/**
 * 处理未找到 UDP 路由情况的接口.
 */
interface IUdpRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @param \Imi\Swoole\Server\UdpServer\Message\IPacketData $data
     * @param \Imi\Swoole\Server\UdpServer\IPacketHandler      $handler
     *
     * @return void
     */
    public function handle(IPacketData $data, IPacketHandler $handler);
}
