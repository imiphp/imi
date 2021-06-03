<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Controller',
        'Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Listener',
        'Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Error',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Middleware\Test::class,
            ],
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                'HandShakeMiddleware',
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => 'ConnectContextLocal',
            'ttl'           => 600,
        ],
        'ConnectContextMemoryTable' => [
            'tableName' => 'connectContext',
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'RouteNotFound',
        ],
        'ConnectionBinder'  => [
            'handlerClass' => 'ConnectionBinderLocal',
        ],
    ],
];
