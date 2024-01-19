<?php

declare(strict_types=1);

return [
    // 配置文件
    'configs'           => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'          => [
        'Imi\SwooleTracker\Example\UDPServer\Listener',
        'Imi\SwooleTracker\Example\UDPServer\Task',
    ],

    // 组件命名空间
    'components'        => [
        'SwooleTracker'       => 'Imi\SwooleTracker',
    ],

    // 主服务器配置
    'mainServer'        => [
        'namespace'     => 'Imi\SwooleTracker\Example\UDPServer\UDPServer',
        'type'          => Imi\Swoole\Server\Type::UDP_SERVER,
        'host'          => '127.0.0.1',
        'port'          => 8083,
        'configs'       => [
            // 'worker_num'        =>  8,
            // 'task_worker_num'   =>  16,
            'max_wait_time' => 30,
        ],
        // 数据处理器
        'dataParser'    => Imi\Server\DataParser\JsonObjectParser::class,
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
        // 'SubServerName'   =>  [
        //     'namespace'    =>    'Imi\SwooleTracker\Example\UDPServer\XXXServer',
        //     'type'        =>    Imi\Swoole\Server\Type::HTTP,
        //     'host'        =>    '127.0.0.1',
        //     'port'        =>    13005,
        // ]
    ],

    // 连接池配置
    'pools'             => [
    ],

    // 数据库配置
    'db'                => [
        // 数默认连接池名
        'defaultPool'    => 'maindb',
    ],

    // redis 配置
    'redis'             => [
        // 数默认连接池名
        'defaultPool'   => 'redis',
    ],

    // 内存表配置
    'memoryTable'       => [
        // 't1'    =>  [
        //     'columns'   =>  [
        //         ['name' => 'name', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 16],
        //         ['name' => 'quantity', 'type' => \Swoole\Table::TYPE_INT],
        //     ],
        //     'lockId'    =>  'atomic',
        // ],
    ],

    // 锁
    'lock'              => [
        'list'  => [
            // 'redis' =>  [
            //     'class' =>  'RedisLock',
            //     'options'   =>  [
            //         'poolName'  =>  'redis',
            //     ],
            // ],
        ],
    ],

    // atmoic 配置
    'atomics'           => [
        // 'atomicLock'   =>  1,
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
