<?php

declare(strict_types=1);

return [
    'configs'     => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\HttpServer\Http2TestServer\Controller',
    ],
    'beans'       => [
        'HttpDispatcher'            => [
            'middlewares'    => [
                \Imi\Swoole\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectionContextStore'    => [
            'handlerClass'  => \Imi\Server\ConnectionContext\StoreHandler\Redis::class,
        ],
        'ConnectionContextRedis'    => [
            'redisPool' => 'redis',
            'lockId'    => 'redisConnectionContextLock',
        ],
    ],
];
