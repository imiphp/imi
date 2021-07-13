<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Smarty\Example\MainServer\Controller',
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
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                \Imi\Smarty\Example\MainServer\Middleware\PoweredBy::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'HtmlView'    => [
            'templatePath'      => dirname(__DIR__) . '/template/',
            'templateEngine'    => 'SmartyEngine',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        => [
                'tpl',
                'html',
                'php',
            ],
        ],
        // 可选项
        'SmartyEngine' => [
            // 缓存目录
            'cacheDir'      => null,
            // 编译目录
            'compileDir'    => null,
            // 是否开启缓存，默认不开启
            'caching'       => 0,
            // 缓存有效时间
            'cacheLifetime' => 0,
        ],
    ],
];
