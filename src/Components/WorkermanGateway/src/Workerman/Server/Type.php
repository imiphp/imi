<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server;

class Type
{
    /**
     * WebSocket 业务服务器.
     */
    const BUSINESS_WEBSOCKET = 'WorkermanGatewayWebSocketBusinessServer';

    /**
     * TCP 业务服务器.
     */
    const BUSINESS_TCP = 'WorkermanGatewayTcpBusinessServer';

    /**
     * 网关服务器.
     */
    const GATEWAY = 'WorkermanGatewayGatewayServer';

    /**
     * 注册中心服务器.
     */
    const REGISTER = 'WorkermanGatewayRegisterServer';

    private function __construct()
    {
    }
}
