<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Workerman\Event\WorkermanEvents;

/**
 * WebSocket 服务器路由初始化.
 */
#[Listener(eventName: WorkermanEvents::SERVER_WORKER_START, one: true)]
class WSRouteInit extends \Imi\Server\WebSocket\Listener\WSRouteInit
{
}
