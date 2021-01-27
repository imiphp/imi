<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Tcp\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * TCP 服务器路由初始化.
 *
 * @Listener("IMI.WORKERMAN.SERVER.WORKER_START")
 */
class TcpRouteInit extends \Imi\Server\TcpServer\Listener\TcpRouteInit
{
}
