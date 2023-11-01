<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Udp\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * UDP 服务器路由初始化.
 */
#[Listener(eventName: 'IMI.WORKERMAN.SERVER.WORKER_START', one: true)]
class UdpRouteInit extends \Imi\Server\UdpServer\Listener\UdpRouteInit
{
}
