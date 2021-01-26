<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * WebSocket 服务器路由初始化.
 *
 * @Listener("IMI.WORKERMAN.SERVER.WORKER_START")
 */
class WSRouteInit extends \Imi\Server\WebSocket\Listener\WSRouteInit
{
}
