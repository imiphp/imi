<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

class Type
{
    /**
     * HTTP 服务器.
     */
    const HTTP = 'WorkermanHttpServer';

    /**
     * WebSocket 服务器.
     */
    const WEBSOCKET = 'WorkermanWebSocketServer';

    /**
     * TCP 服务器.
     */
    const TCP = 'WorkermanTcpServer';

    /**
     * UDP 服务器.
     */
    const UDP = 'WorkermanUdpServer';

    /**
     * Channel 服务器.
     */
    const CHANNEL = 'WorkermanChannelServer';

    private function __construct()
    {
    }
}
