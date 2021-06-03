<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\WebSocketServer\MainServer\Controller',
        'Imi\Swoole\Test\WebSocketServer\MainServer\Listener',
        'Imi\Swoole\Test\WebSocketServer\MainServer\Error',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Swoole\Test\WebSocketServer\MainServer\Middleware\Test::class,
            ],
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupRedis',
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'redisDb'      => 2,
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                'HandShakeMiddleware',
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
            'handlerClass' => 'ConnectionBinderRedis',
        ],
        'ConnectionBinderRedis' => [
            'redisPool' => 'redis',
            'key'       => 'imi:wsTest:connectionBinder:map',
        ],
    ],
];
