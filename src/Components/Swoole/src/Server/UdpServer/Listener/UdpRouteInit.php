<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * UDP 服务器路由初始化.
 *
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class UdpRouteInit extends \Imi\Server\UdpServer\Listener\UdpRouteInit
{
}
