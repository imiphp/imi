<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'KafkaApp\ApiServer\Controller',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
    ],
];
