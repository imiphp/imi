<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server;

class Type
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * WebSocket 业务服务器.
     */
    public const BUSINESS_WEBSOCKET = 'SwooleGatewayWebSocketBusinessServer';

    /**
     * TCP 业务服务器.
     */
    public const BUSINESS_TCP = 'SwooleGatewayTcpBusinessServer';
}
