<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\HttpServer\SessionTestServer\Controller',
    ],
    'beans'    => [
        'SessionManager'    => [
            'handlerClass'    => \Imi\Server\Session\Handler\File::class,
        ],
        'SessionFile'    => [
            'savePath'    => \dirname(__DIR__, 2) . '/.runtime/.session2',
        ],
        'SessionConfig'    => [
        ],
        'SessionCookie'    => [
            'enable'    => false,
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Swoole\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'HtmlView'    => [
            'templatePath'    => \dirname(__DIR__) . '/template/',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        => [
                'tpl',
                'html',
                'php',
            ],
        ],
        \Imi\Server\Session\Middleware\HttpSessionMiddleware::class => [
            'sessionIdHandler'  => static fn (Imi\Server\Http\Message\Request $request) => $request->getHeaderLine('X-Session-ID'),
        ],
    ],
];
