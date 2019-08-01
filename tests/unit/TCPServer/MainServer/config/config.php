<?php

return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'Imi\Test\TCPServer\MainServer\Controller',
        'Imi\Test\TCPServer\MainServer\Listener',
    ],
    'beans'    =>    [
        'TcpDispatcher'    =>    [
            'middlewares'    =>    [
                \Imi\Server\TcpServer\Middleware\RouteMiddleware::class,
            ],
        ],
        'GroupRedis'    =>    [
            'redisPool'    =>    'redis',
            'key'        =>    'IMITEST.TCP.GROUP',
        ],
    ],
];