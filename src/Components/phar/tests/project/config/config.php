<?php

declare(strict_types=1);

use Imi\App;

$mode = App::isInited() ? App::getApp()->getType() : null;

return [
    // 运行时目录
    'runtimePath'       => app_real_root_path() . '/.runtime',

    // 配置文件
    'configs'           => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    'ignoreNamespace'   => [
    ],

    'ignorePaths'       => [
        \dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'public',
        \dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'rr',
    ],

    // Swoole 主服务器配置
    'mainServer'        => 'swoole' === $mode ? [
        'namespace'    => 'ImiApp\ApiServer',
        'type'         => Imi\Swoole\Server\Type::HTTP,
        'host'         => '0.0.0.0',
        'port'         => 13000,
        'configs'      => [
            'log_file'      => app_real_root_path() . '/.runtime/swoole/swoole.log',
            'max_wait_time' => 30,
            // 'worker_num'        =>  8,
            // 'task_worker_num'   =>  16,
        ],
    ] : [],

    // Swoole 子服务器（端口监听）配置
    'subServers'        => 'swoole' === $mode ? [
        // 'SubServerName'   =>  [
        //     'namespace'    =>    'ImiApp\XXXServer',
        //     'type'        =>    Imi\Server\Type::HTTP,
        //     'host'        =>    '0.0.0.0',
        //     'port'        =>    13005,
        // ]
    ] : [],

    // Workerman 服务器配置
    'workermanServer'   => 'workerman' === $mode ? [
        'http' => [
            'namespace' => 'ImiApp\ApiServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => '0.0.0.0',
            'port'      => 13000,
            'configs'   => [
            ],
        ],
    ] : [],

    'workerman'       => [
        'worker' => [
            'stopTimeout' => 30,
        ],
    ],

    // fpm 服务器配置
    'fpm'               => 'fpm' === $mode ? [
        'serverPath' => \dirname(__DIR__) . '/ApiServer',
    ] : [],

    // roadrunner 服务器配置
    'roadRunnerServer'  => [
        'main' => [
            'namespace' => 'ImiApp\ApiServer',
        ],
    ],

    // 日志配置
    'logger'            => [
        'channels' => [
            'imi' => [
                'handlers' => [
                    [
                        'env'       => ['cli', 'swoole', 'workerman'],
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
                    // RoadRunner worker 下日志
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
                            'filename' => app_real_root_path() . '/.runtime/logs/log.log',
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
];
