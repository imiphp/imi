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
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextMemoryTable',
        ],
        'ConnectionContextMemoryTable' => [
            'tableName' => 'ConnectionContext',
        ],
        'ActionWrapMiddleware'  => [
            'actionMiddleware'  => 'GrpcActionMiddleware',
        ],
    ],
];
