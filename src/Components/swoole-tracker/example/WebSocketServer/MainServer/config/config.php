<?php

use Imi\Util\Http\Consts\StatusCode;

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\SwooleTracker\Example\WebSocketServer\MainServer\Controller',
        'Imi\SwooleTracker\Example\WebSocketServer\MainServer\Listener',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                'SwooleTrackerWebSocketMiddleware',
            ],
        ],
        'SwooleTrackerWebSocketMiddleware'  => [
            'serviceName'       => 'imi-websocket-example', // 服务名
            // 'serverIp'          => null, // 服务器 IP，默认获取当前网卡 IP
            // 'interface'         => null, // 网卡 interface 名，自动获取当前网卡IP时有效
            // 'successCode'       =>  500, // 当成功时上报的默认code
            // 'exceptionCode'     =>  500, // 当发生异常时上报的默认code
            'nameHandler'       => function (Imi\Server\WebSocket\Message\IFrame $frame) {
                return $frame->getFormatData()->action ?? 'unknown';
            },
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'redisDb'      => 2,
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Swoole\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
                'SwooleTrackerHttpMiddleware',
            ],
        ],
        'SwooleTrackerHttpMiddleware'   => [
            'serviceName'       => 'imi-websocket-example-handshake', // 服务名
            // 'serverIp'          => null, // 服务器 IP，默认获取当前网卡 IP
            // 'interface'         => null, // 网卡 interface 名，自动获取当前网卡IP时有效
            'successStatusCode' => StatusCode::SWITCHING_PROTOCOLS, // 成功的 Http 状态码，这里改为握手成功的状态码101
            // 'successCode'       =>  500, // 当成功时上报的默认code
            // 'exceptionCode'     =>  500, // 当发生异常时上报的默认code
        ],
        'ConnectionContextRedis'    => [
            'redisPool'    => 'redis',
        ],
        'ConnectionContextStore'   => [
            'handlerClass'  => \Imi\Swoole\Server\ConnectionContext\StoreHandler\MemoryTable::class,
        ],
        'ConnectionContextMemoryTable' => [
            'tableName' => 'ConnectionContext',
        ],
    ],
];
