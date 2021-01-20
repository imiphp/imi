<?php

declare(strict_types=1);

use Imi\Log\LogLevel;

$rootPath = dirname(__DIR__) . '/';

return [
    'Logger'    => [
        'exHandlers'    => [
            // info 级别日志不输出trace
            [
                'class'        => \Imi\Log\Handler\File::class,
                'options'      => [
                    'levels'        => [LogLevel::INFO],
                    'fileName'      => dirname(__DIR__) . '/logs/{Y}-{m}-{d}.log',
                    'format'        => '{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}',
                ],
            ],
            // 指定级别日志输出trace
            [
                'class'        => \Imi\Log\Handler\File::class,
                'options'      => [
                    'levels'        => [
                        LogLevel::ALERT,
                        LogLevel::CRITICAL,
                        LogLevel::DEBUG,
                        LogLevel::EMERGENCY,
                        LogLevel::ERROR,
                        LogLevel::NOTICE,
                        LogLevel::WARNING,
                    ],
                    'fileName'      => dirname(__DIR__) . '/logs/{Y}-{m}-{d}.log',
                    'format'        => "{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}\n{trace}",
                    'traceFormat'   => '#{index}  {call} called at [{file}:{line}]',
                ],
            ],
        ],
    ],
    'SessionManager'    => [
        'handlerClass'    => \Imi\Server\Session\Handler\File::class,
    ],
    'SessionFile'    => [
        'savePath'    => dirname(__DIR__) . '/.session/',
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
];
