<?php

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\SwooleTracker\Example\HttpServer\ApiServer\Controller',
    ],
    'beans'    => [
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
                'SwooleTrackerHttpMiddleware',
            ],
        ],
        'SwooleTrackerHttpMiddleware'   => [
            'serviceName'       => 'imi-http-example', // 服务名
            // 'serverIp'          => null, // 服务器 IP，默认获取当前网卡 IP
            // 'interface'         => null, // 网卡 interface 名，自动获取当前网卡IP时有效
            // 'successStatusCode' =>  200, // 成功的 Http 状态码
            // 'successCode'       =>  500, // 当成功时上报的默认code
            // 'exceptionCode'     =>  500, // 当发生异常时上报的默认code
        ],
    ],
];
