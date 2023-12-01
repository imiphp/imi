<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Event\SwooleEvents;

/**
 * WebSocket 服务器路由初始化.
 */
#[Listener(eventName: SwooleEvents::SERVER_WORKER_START, one: true)]
class WSRouteInit extends \Imi\Server\WebSocket\Listener\WSRouteInit
{
}
