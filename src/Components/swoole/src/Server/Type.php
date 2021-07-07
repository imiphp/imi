<?php

declare(strict_types=1);

namespace Imi\Swoole\Server;

class Type
{
    /**
     * 异步服务器，支持TCP、UDP、UnixSocket.
     */
    public const SERVER = 'Server';

    /**
     * HTTP服务器.
     */
    public const HTTP = 'HttpServer';

    /**
     * WebSocket服务器.
     */
    public const WEBSOCKET = 'WebSocketServer';

    /**
     * 异步服务器，支持TCP、UDP、UnixSocket.
     */
    public const TCP_SERVER = 'TcpServer';

    /**
     * 异步服务器，支持TCP、UDP、UnixSocket.
     */
    public const UDP_SERVER = 'UdpServer';

    private function __construct()
    {
    }
}
