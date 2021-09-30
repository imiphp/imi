<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__) . \DIRECTORY_SEPARATOR;

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
                        'env'       => ['cli'],
                        'class'     => \Imi\Log\Handler\ConsoleHandler::class,
                        'formatter' => [
                            'class'     => \Imi\Log\Formatter\ConsoleLineFormatter::class,
                            'construct' => [
                                'format'                     => null,
                                'dateFormat'                 => 'Y-m-d H:i:s',
                                'allowInlineLineBreaks'      => true,
                                'ignoreEmptyContextAndExtra' => true,
                            ],
                        ],
                    ],
                    [
                        'env'       => ['roadrunner'],
                        'class'     => \Monolog\Handler\StreamHandler::class,
                        'construct' => [
                            'stream'  => 'php://stderr',
                        ],
                        'formatter' => [
                            'class'     => \Monolog\Formatter\LineFormatter::class,
                            'construct' => [
                                'format'                     => null,
                                'dateFormat'                 => 'Y-m-d H:i:s',
                                'allowInlineLineBreaks'      => true,
                                'ignoreEmptyContextAndExtra' => true,
                            ],
                        ],
                    ],
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
        'hotUpdate'    => [
            'status'    => false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

            // --- 文件修改时间监控 ---
            // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\FileMTime::class,
            'timespan'    => 1, // 检测时间间隔，单位：秒

            // --- Inotify 扩展监控 ---
            // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\Inotify::class,
            // 'timespan'    =>    1, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

            // 'includePaths'    =>    [], // 要包含的路径数组
            'excludePaths'    => [
                $rootPath . '.git',
                $rootPath . 'bin',
                $rootPath . 'logs',
                $rootPath . '.session',
            ], // 要排除的路径数组，支持通配符*
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
