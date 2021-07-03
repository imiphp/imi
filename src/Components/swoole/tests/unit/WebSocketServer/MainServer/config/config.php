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
                \Imi\Swoole\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectionContextRedis'    => [
            'redisPool' => 'redis',
            'lockId'    => 'redisConnectionContextLock',
        ],
        'ConnectionContextStore'   => [
            'handlerClass'  => \Imi\Server\ConnectionContext\StoreHandler\Redis::class,
            'ttl'           => 600,
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'RouteNotFound',
        ],
    ],
];
