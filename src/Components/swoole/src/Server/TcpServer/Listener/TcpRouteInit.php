<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Event\SwooleEvents;

/**
 * TCP 服务器路由初始化.
 */
#[Listener(eventName: SwooleEvents::SERVER_WORKER_START, one: true)]
class TcpRouteInit extends \Imi\Server\TcpServer\Listener\TcpRouteInit
{
}
