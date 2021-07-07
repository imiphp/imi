<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

class Type
{
    /**
     * HTTP 服务器.
     */
    public const HTTP = 'WorkermanHttpServer';

    /**
     * WebSocket 服务器.
     */
    public const WEBSOCKET = 'WorkermanWebSocketServer';

    /**
     * TCP 服务器.
     */
    public const TCP = 'WorkermanTcpServer';

    /**
     * UDP 服务器.
     */
    public const UDP = 'WorkermanUdpServer';

    /**
     * Channel 服务器.
     */
    public const CHANNEL = 'WorkermanChannelServer';

    private function __construct()
    {
    }
}
