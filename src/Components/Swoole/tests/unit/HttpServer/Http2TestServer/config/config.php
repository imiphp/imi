<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\HttpServer\Http2TestServer\Controller',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Swoole\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => \Imi\Swoole\Server\ConnectContext\StoreHandler\Redis::class,
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
