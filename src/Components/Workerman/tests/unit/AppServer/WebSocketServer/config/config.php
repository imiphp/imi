<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\AppServer\WebSocketServer\Controller',
        'Imi\Workerman\Test\AppServer\WebSocketServer\Error',
        'Imi\Workerman\Test\AppServer\WebSocketServer\Middleware',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Swoole\Test\WebSocketServer\MainServer\Middleware\Test::class,
            ],
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'redisDb'      => 2,
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => 'ConnectContextLocal',
            'ttl'           => 600,
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'WSRouteNotFound',
        ],
        'ConnectionBinder'  => [
            'redisPool' => 'redis',
            'key'       => 'imi:wsTest:connectionBinder:map',
        ],
    ],
    // 锁配置
    'lock'  => [
        'default' => 'redisConnectContextLock',
        'list'    => [
            'redisConnectContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
    ],
];
