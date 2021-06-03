<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server;

class Type
{
    /**
     * WebSocket 业务服务器.
     */
    const BUSINESS_WEBSOCKET = 'SwooleGatewayWebSocketBusinessServer';

    /**
     * TCP 业务服务器.
     */
    const BUSINESS_TCP = 'SwooleGatewayTcpBusinessServer';

    private function __construct()
    {
    }
}
