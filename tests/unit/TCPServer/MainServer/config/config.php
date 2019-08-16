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
                \Imi\Test\TCPServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\TcpServer\Middleware\RouteMiddleware::class,
            ],
        ],
        'GroupRedis'    =>    [
            'redisPool'    =>    'redis',
            'key'        =>    'IMITEST.TCP.GROUP',
            'redisDb'   =>  2,
        ],
        'ConnectContextStore'   =>  [
            'handlerClass'  =>  \Imi\Server\ConnectContext\StoreHandler\MemoryTable::class,
        ],
        'ConnectContextMemoryTable' =>  [
            'tableName' =>  'connectContext',
        ],
    ],
];