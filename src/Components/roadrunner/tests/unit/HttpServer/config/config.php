<?php

declare(strict_types=1);

return [
    // 项目根命名空间
    'namespace'        => 'Imi\RoadRunner\Test\HttpServer',
    // 组件命名空间
    'components'    => [
        'RoadRunner' => 'Imi\RoadRunner',
    ],
    'roadRunnerServer' => [
        'main' => [],
    ],
    'ignorePaths'   => [
        dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'bin',
        dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'public',
        dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'Tests',
    ],
    // 日志配置
    'logger' => [
        'channels' => [
            'imi' => [
                'handlers' => [
                    [
                        'class'     => \Monolog\Handler\RotatingFileHandler::class,
                        'construct' => [
                            'filename' => dirname(__DIR__) . '/logs/log.log',
                        ],
                        'formatter' => [
                            'class'     => \Monolog\Formatter\LineFormatter::class,
                            'construct' => [
                                'dateFormat'                 => 'Y-m-d H:i:s',
                                'allowInlineLineBreaks'      => true,
                                'ignoreEmptyContextAndExtra' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'middleware'    => [
        'groups'    => [
            'test'  => [
                \Imi\RoadRunner\Test\HttpServer\Middleware\Middleware4::class,
            ],
        ],
    ],
    'beans' => [
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
                \Imi\RoadRunner\Test\HttpServer\Middleware\PoweredBy::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
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
        'OptionsMiddleware' => [
            'allowOrigin'   => 'http://127.0.0.1',
            'optionsBreak'  => true,
        ],
    ],
];
