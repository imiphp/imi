<?php

return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'Imi\Test\WebSocketServer\MainServer\Controller',
        'Imi\Test\WebSocketServer\MainServer\Listener',
    ],
    'beans'    =>    [
        'WebSocketDispatcher'    =>    [
            'middlewares'    =>    [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Test\WebSocketServer\MainServer\Middleware\Test::class,
            ],
        ],
        'GroupRedis'    =>    [
            'redisPool'    =>    'redis',
            'redisDb'   =>  2,
        ],
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                \Imi\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectContextRedis'    =>    [
            'redisPool'    =>    'redis',
        ],
        'ConnectContextStore'   =>  [
            'handlerClass'  =>  \Imi\Server\ConnectContext\StoreHandler\MemoryTable::class,
        ],
        'ConnectContextMemoryTable' =>  [
            'tableName' =>  'connectContext',
        ],
    ],
    'controller'    =>  [
        'singleton' =>  true,
    ],
];