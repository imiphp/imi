<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\HttpServer\HttpsTestServer\Controller',
        'Imi\Swoole\Test\HttpServer\OutsideController',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Swoole\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
    ],
];
