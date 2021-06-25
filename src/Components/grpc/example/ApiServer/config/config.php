<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'GrpcApp\ApiServer\Controller',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            'middlewares'    => [
                \GrpcApp\GrpcServer\Middleware\PoweredBy::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
    ],
];
