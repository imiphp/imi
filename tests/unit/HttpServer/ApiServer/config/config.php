<?php

use Imi\Log\LogLevel;
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'Imi\Test\HttpServer\ApiServer\Controller',
        'Imi\Test\HttpServer\Modules',
    ],
    'beans'    =>    [
        // 'SessionManager'    =>    [
        //     'handlerClass'    =>    \Imi\Server\Session\Handler\Redis::class,
        // ],
        // 'SessionConfig'    =>    [

        // ],
        // 'SessionCookie'    =>    [
        //     'lifetime'    =>    86400 * 30,
        // ],
        // 'SessionRedis'    =>    [
        //     'poolName'              => 'redisSession',
        //     'formatHandlerClass'    => \Imi\Util\Format\Json::class,
        //     'keyPrefix'             => 'session:',
        // ],
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                // \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                \Imi\Test\HttpServer\ApiServer\Middleware\PoweredBy::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'HtmlView'    =>    [
            'templatePath'    =>    dirname(__DIR__) . '/template/',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        =>    [
                'tpl',
                'html',
                'php'
            ],
        ]
    ],
];