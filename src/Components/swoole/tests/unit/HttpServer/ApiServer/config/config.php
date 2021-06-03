<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\HttpServer\ApiServer\Controller',
        'Imi\Swoole\Test\HttpServer\Modules',
        'Imi\Swoole\Test\HttpServer\OutsideController',
        'Imi\Swoole\Test\HttpServer\ApiServer\Error',
    ],
    'beans'    => [
        'SessionManager'    => [
            'handlerClass'    => \Imi\Server\Session\Handler\File::class,
        ],
        'SessionFile'    => [
            'savePath'    => dirname(__DIR__, 2) . '/.session/',
        ],
        'SessionConfig'    => [
        ],
        'SessionCookie'    => [
            'lifetime'    => 86400 * 30,
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                'OptionsMiddleware',
                'ExecuteTimeoutMiddleware',
                \Imi\Swoole\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                \Imi\Swoole\Test\HttpServer\ApiServer\Middleware\PoweredBy::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'OptionsMiddleware' => [
            'allowOrigin'   => 'http://127.0.0.1',
            'optionsBreak'  => true,
        ],
        'HtmlView'    => [
            'templatePath'    => dirname(__DIR__) . '/template/',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        => [
                'tpl',
                'html',
                'php',
            ],
        ],
        'ExecuteTimeoutMiddleware' => [
            'maxExecuteTime'    => 3000,
        ],
        'HttpNotFoundHandler'   => [
            'handler'   => 'MyHttpNotFoundHandler',
        ],
    ],
    'middleware'    => [
        'groups'    => [
            'test'  => [
                \Imi\Swoole\Test\HttpServer\ApiServer\Middleware\Middleware4::class,
            ],
        ],
    ],
];
