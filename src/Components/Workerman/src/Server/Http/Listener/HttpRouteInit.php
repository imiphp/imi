<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * http服务器路由初始化.
 *
 * @Listener("IMI.WORKERMAN.SERVER.WORKER_START")
 */
class HttpRouteInit extends \Imi\Server\Http\Listener\HttpRouteInit
{
}
