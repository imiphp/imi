<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Workerman\Event\WorkermanEvents;

/**
 * http服务器路由初始化.
 */
#[Listener(eventName: WorkermanEvents::SERVER_WORKER_START, one: true)]
class HttpRouteInit extends \Imi\Server\Http\Listener\HttpRouteInit
{
}
