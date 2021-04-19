<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\WorkermanGateway\Test\WorkermanServer\WebSocketServer\Controller',
        'Imi\WorkermanGateway\Test\WorkermanServer\WebSocketServer\Error',
        'Imi\WorkermanGateway\Test\WorkermanServer\WebSocketServer\Middleware',
        'Imi\WorkermanGateway\Test\WorkermanServer\WebSocketServer\Listener',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\WorkermanGateway\Test\WorkermanServer\WebSocketServer\Middleware\Test::class,
            ],
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                'HandShakeMiddleware',
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'WSRouteNotFound',
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => 'ConnectionContextGateway',
        ],
        'ConnectionBinder'  => [
            'handlerClass' => 'ConnectionBinderGateway',
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupGateway',
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
