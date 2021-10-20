<?php

declare(strict_types=1);

$rootPath = \dirname(__DIR__) . '/';

return [
    'SessionManager'    => [
        'handlerClass'    => \Imi\Server\Session\Handler\File::class,
    ],
    'SessionFile'    => [
        'savePath'    => \dirname(__DIR__) . '/.session/',
    ],
    'SessionConfig'    => [
    ],
    'SessionCookie'    => [
        'lifetime'    => 86400 * 30,
    ],
    'HttpDispatcher'    => [
        'middlewares'    => [
            'OptionsMiddleware',
            \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
            \Imi\Fpm\Test\Web\Middleware\PoweredBy::class,
            \Imi\Server\Http\Middleware\RouteMiddleware::class,
        ],
    ],
    'OptionsMiddleware' => [
        'allowOrigin'   => 'http://127.0.0.1',
        'optionsBreak'  => true,
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
    'HttpNotFoundHandler'   => [
        'handler'   => 'MyHttpNotFoundHandler',
    ],
];
