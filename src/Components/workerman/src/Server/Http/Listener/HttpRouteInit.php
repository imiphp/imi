<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * http服务器路由初始化.
 */
#[Listener(eventName: 'IMI.WORKERMAN.SERVER.WORKER_START', one: true)]
class HttpRouteInit extends \Imi\Server\Http\Listener\HttpRouteInit
{
}
