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
        'Imi\Workerman\Test\AppServer\WebSocketServer\Listener',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Workerman\Test\AppServer\WebSocketServer\Middleware\Test::class,
            ],
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'redisDb'      => 2,
        ],
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextLocal',
            'ttl'           => 600,
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'WSRouteNotFound',
        ],
    ],
    // 锁配置
    'lock'  => [
        'default' => 'redisConnectionContextLock',
        'list'    => [
            'redisConnectionContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
    ],
];
