<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * WebSocket 服务器路由初始化.
 *
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START", one=true)
 */
class WSRouteInit extends \Imi\Server\WebSocket\Listener\WSRouteInit
{
}
