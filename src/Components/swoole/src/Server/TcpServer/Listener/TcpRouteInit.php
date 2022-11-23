<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Listener;

use Imi\Bean\Annotation\Listener;

/**
 * TCP 服务器路由初始化.
 *
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START", one=true)
 */
class TcpRouteInit extends \Imi\Server\TcpServer\Listener\TcpRouteInit
{
}
