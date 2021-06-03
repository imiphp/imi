<?php

return [
    // 项目根命名空间
    'namespace'    => 'Imi\Smarty\Example',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'Imi\Smarty\Example\Listener',
        'Imi\Smarty\Example\Task',
    ],

    // 组件命名空间
    'components'    => [
        'Swoole' => 'Imi\Swoole',
        'Smarty' => 'Imi\Smarty',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'    => 'Imi\Smarty\Example\MainServer',
        'type'         => Imi\Swoole\Server\Type::HTTP,
        'host'         => '127.0.0.1',
        'port'         => 13456,
        'configs'      => [
            'worker_num'        => 1,
            'task_worker_num'   => 1,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
    ],

    // 连接池配置
    'pools'    => [
        // 主数据库
        'maindb'    => [
            'pool'    => [
                'class'        => \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
                'config'       => [
                    'maxResources'    => 10,
                    'minResources'    => 0,
                ],
            ],
            'resource'    => [
                'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                'username'    => 'root',
                'password'    => 'root',
                'database'    => 'mysql',
                'charset'     => 'utf8mb4',
            ],
        ],
        'redis'    => [
            'pool'    => [
                'class'        => \Imi\Swoole\Redis\Pool\CoroutineRedisPool::class,
                'config'       => [
                    'maxResources'    => 10,
                    'minResources'    => 0,
                ],
            ],
            'resource'    => [
                'host'        => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'        => 6379,
            ],
        ],
    ],

    // 数据库配置
    'db'    => [
        // 数默认连接池名
        'defaultPool'    => 'maindb',
    ],

    // redis 配置
    'redis' => [
        // 数默认连接池名
        'defaultPool'   => 'redis',
    ],

    // 内存表配置
    'memoryTable'   => [
        't1'    => [
            'columns'   => [
                ['name' => 'name', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 16],
                ['name' => 'quantity', 'type' => \Swoole\Table::TYPE_INT],
            ],
            'lockId'    => 'atomic',
        ],
    ],

    // 锁
    'lock'  => [
        'list'  => [
            'atomic' => [
                'class'     => 'AtomicLock',
                'options'   => [
                    'atomicName'    => 'atomicLock',
                ],
            ],
        ],
    ],

    // atmoic 配置
    'atomics'    => [
        'atomicLock'   => 1,
    ],
    // 日志配置
    'logger' => [
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
                            'filename' => dirname(__DIR__) . '/.runtime/logs/log.log',
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
