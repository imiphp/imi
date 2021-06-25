<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'AMQPApp\ApiServer\Controller',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            // TODO：注入失败
            'middlewares'    => [
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
    ],
];
