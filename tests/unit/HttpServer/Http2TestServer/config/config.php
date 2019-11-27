<?php

use Imi\Log\LogLevel;
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'Imi\Test\HttpServer\Http2TestServer\Controller',
    ],
    'beans'    =>    [
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                \Imi\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectContextStore'   =>  [
            'handlerClass'  =>  \Imi\Server\ConnectContext\StoreHandler\MemoryTable::class,
        ],
        'ConnectContextMemoryTable' =>  [
            'tableName' =>  'connectContext',
        ],
    ],
];