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
        'ConnectContextStore'   => [
            'handlerClass'  => 'ConnectContextLocal',
            'ttl'           => 600,
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'WSRouteNotFound',
        ],
        'ConnectionBinder'  => [
            'handlerClass' => 'ConnectionBinderRedis',
        ],
        'ConnectionBinderRedis' => [
            'redisPool' => 'redis',
            'key'       => 'imi:ChannelServerUtilServer:connectionBinder:map',
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
