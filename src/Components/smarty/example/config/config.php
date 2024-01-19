<?php

declare(strict_types=1);

return [
    // 配置文件
    'configs'           => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'          => [
        'Imi\Smarty\Example\Listener',
        'Imi\Smarty\Example\Task',
    ],

    // 组件命名空间
    'components'        => [
        'Swoole' => 'Imi\Swoole',
        'Smarty' => 'Imi\Smarty',
    ],

    // 主服务器配置
    'mainServer'        => [
        'namespace'    => 'Imi\Smarty\Example\MainServer',
        'type'         => Imi\Swoole\Server\Type::HTTP,
        'host'         => '127.0.0.1',
        'port'         => 13456,
        'configs'      => [
            'worker_num'        => 1,
            'task_worker_num'   => 1,
            'max_wait_time'     => 30,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
    ],

    // 日志配置
    'logger'            => [
        'channels' => [
            'imi' => [
                'handlers' => [
                    [
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
                        'class'     => \Monolog\Handler\RotatingFileHandler::class,
                        'construct' => [
                            'filename' => \dirname(__DIR__) . '/.runtime/logs/log.log',
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
