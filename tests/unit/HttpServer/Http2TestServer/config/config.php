<?php

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Test\HttpServer\Http2TestServer\Controller',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => \Imi\Server\ConnectContext\StoreHandler\Redis::class,
        ],
        'ConnectContextRedis'    => [
            'redisPool' => 'redis',
            'lockId'    => 'redisConnectContextLock',
        ],
        'ConnectContextMemoryTable' => [
            'tableName' => 'connectContext',
        ],
    ],
];
