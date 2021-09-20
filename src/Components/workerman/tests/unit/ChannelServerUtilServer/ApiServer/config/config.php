<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\ChannelServerUtilServer\ApiServer\Controller',
        'Imi\Workerman\Test\ChannelServerUtilServer\Modules',
        'Imi\Workerman\Test\ChannelServerUtilServer\OutsideController',
        'Imi\Workerman\Test\ChannelServerUtilServer\ApiServer\Error',
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
                \Imi\Workerman\Test\AppServer\ApiServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
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
        'HttpNotFoundHandler'   => [
            'handler'   => 'MyHttpNotFoundHandler',
        ],
    ],
    'middleware'    => [
    ],
];
