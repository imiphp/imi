<?php

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Test\TCPServer\MainServer\Controller',
        'Imi\Test\TCPServer\MainServer\Listener',
        'Imi\Test\TCPServer\MainServer\Error',
    ],
    'beans'    => [
        'TcpDispatcher'    => [
            'middlewares'    => [
                \Imi\Test\TCPServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\TcpServer\Middleware\RouteMiddleware::class,
                \Imi\Test\TCPServer\MainServer\Middleware\Test::class,
            ],
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'key'          => 'IMITEST.TCP.GROUP',
            'redisDb'      => 2,
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => \Imi\Server\ConnectContext\StoreHandler\MemoryTable::class,
        ],
        'ConnectContextMemoryTable' => [
            'tableName' => 'connectContext',
        ],
        'TcpRouteNotFoundHandler'   => [
            'handler'   => 'RouteNotFound',
        ],
    ],
    'controller'    => [
        'singleton' => true,
    ],
];
