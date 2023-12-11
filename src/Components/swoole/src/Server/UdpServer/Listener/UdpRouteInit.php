<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Event\SwooleEvents;

/**
 * UDP 服务器路由初始化.
 */
#[Listener(eventName: SwooleEvents::SERVER_WORKER_START, one: true)]
class UdpRouteInit extends \Imi\Server\UdpServer\Listener\UdpRouteInit
{
}
