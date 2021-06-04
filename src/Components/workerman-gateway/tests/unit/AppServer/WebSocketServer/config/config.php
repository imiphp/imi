<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Controller',
        'Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Error',
        'Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Middleware',
        'Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Listener',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Middleware\Test::class,
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
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextGateway',
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupGateway',
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
