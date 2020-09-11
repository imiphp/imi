<?php

namespace Imi\Server;

class Type
{
    /**
     * 异步服务器，支持TCP、UDP、UnixSocket.
     */
    const SERVER = 'Server';

    /**
     * HTTP服务器.
     */
    const HTTP = 'Http';

    /**
     * WebSocket服务器.
     */
    const WEBSOCKET = 'WebSocket';

    /**
     * 异步服务器，支持TCP、UDP、UnixSocket.
     */
    const TCP_SERVER = 'TcpServer';

    /**
     * 异步服务器，支持TCP、UDP、UnixSocket.
     */
    const UDP_SERVER = 'UdpServer';
}
