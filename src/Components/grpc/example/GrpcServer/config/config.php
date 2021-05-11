<?php

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'GrpcApp\GrpcServer\Controller',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            'middlewares'    => [
                \GrpcApp\GrpcServer\Middleware\PoweredBy::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => 'ConnectContextMemoryTable',
        ],
        'ConnectContextMemoryTable' => [
            'tableName' => 'connectContext',
        ],
        'ActionWrapMiddleware'  => [
            'actionMiddleware'  => 'GrpcActionMiddleware',
        ],
    ],
];
