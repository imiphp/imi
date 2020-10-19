<?php

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Test\WebSocketServer\MainServer\Controller',
        'Imi\Test\WebSocketServer\MainServer\Listener',
        'Imi\Test\WebSocketServer\MainServer\Error',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Test\WebSocketServer\MainServer\Middleware\Test::class,
            ],
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'redisDb'      => 2,
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectContextRedis'    => [
            'redisPool' => 'redis',
            'lockId'    => 'redisConnectContextLock',
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => \Imi\Server\ConnectContext\StoreHandler\Redis::class,
            'ttl'           => 600,
        ],
        'ConnectContextMemoryTable' => [
            'tableName' => 'connectContext',
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'RouteNotFound',
        ],
        'ConnectionBinder'  => [
            'redisPool' => 'redis',
            'key'       => 'imi:wsTest:connectionBinder:map',
        ],
    ],
    'controller'    => [
        'singleton' => true,
    ],
];
