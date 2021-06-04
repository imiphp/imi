<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\ChannelServerUtilServer\WebSocketServer\Controller',
        'Imi\Workerman\Test\ChannelServerUtilServer\WebSocketServer\Error',
        'Imi\Workerman\Test\ChannelServerUtilServer\WebSocketServer\Middleware',
        'Imi\Workerman\Test\ChannelServerUtilServer\WebSocketServer\Listener',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
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
