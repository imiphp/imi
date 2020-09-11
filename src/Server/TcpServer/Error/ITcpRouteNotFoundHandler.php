<?php

namespace Imi\Server\TcpServer\Error;

use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * 处理未找到 TCP 路由情况的接口.
 */
interface ITcpRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @param \Imi\Server\TcpServer\Message\IReceiveData $data
     * @param \Imi\Server\TcpServer\IReceiveHandler      $handler
     *
     * @return void
     */
    public function handle(IReceiveData $data, IReceiveHandler $handler);
}
