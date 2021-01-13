<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Error;

use Imi\Swoole\Server\TcpServer\IReceiveHandler;
use Imi\Swoole\Server\TcpServer\Message\IReceiveData;

/**
 * 处理未找到 TCP 路由情况的接口.
 */
interface ITcpRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @param \Imi\Swoole\Server\TcpServer\Message\IReceiveData $data
     * @param \Imi\Swoole\Server\TcpServer\IReceiveHandler      $handler
     *
     * @return mixed
     */
    public function handle(IReceiveData $data, IReceiveHandler $handler);
}
