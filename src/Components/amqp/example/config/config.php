<?php

defined('AMQP_SERVER_HOST') || define('AMQP_SERVER_HOST', imiGetEnv('AMQP_SERVER_HOST', '127.0.0.1'));

return [
    // 项目根命名空间
    'namespace'    => 'AMQPApp',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'AMQPApp\Listener',
        'AMQPApp\Task',
        'AMQPApp\Consumer',
        'AMQPApp\AMQP',
        'AMQPApp\Process',
    ],

    // 组件命名空间
    'components'    => [
        'AMQP'  => 'Imi\AMQP',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'    => 'AMQPApp\ApiServer',
        'type'         => Imi\Swoole\Server\Type::HTTP,
        'host'         => '127.0.0.1',
        'port'         => 8080,
        'configs'      => [
            'worker_num'        => 1,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
    ],

    // 连接池配置
    'pools'    => [
        'redis'    => [
            'sync'    => [
                'pool'    => [
                    'class'        => \Imi\Redis\SyncRedisPool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 0,
                    ],
                ],
                'resource'    => [
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => 6379,
                    'password'  => null,
                ],
            ],
            'async'    => [
                'pool'    => [
                    'class'        => \Imi\Swoole\Redis\Pool\CoroutineRedisPool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 1,
                    ],
                ],
                'resource'    => [
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => 6379,
                    'password'  => null,
                ],
            ],
        ],
        'rabbit'    => [
            'sync'    => [
                'pool'    => [
                    'class'        => \Imi\AMQP\Pool\AMQPSyncPool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 0,
                    ],
                ],
                'resource'    => [
                    'host'      => AMQP_SERVER_HOST,
                    'port'      => 5672,
                    'user'      => 'guest',
                    'password'  => 'guest',
                ],
            ],
            'async'    => [
                'pool'    => [
                    'class'        => \Imi\AMQP\Pool\AMQPCoroutinePool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 1,
                    ],
                ],
                'resource'    => [
                    'host'      => AMQP_SERVER_HOST,
                    'port'      => 5672,
                    'user'      => 'guest',
                    'password'  => 'guest',
                ],
            ],
        ],
    ],

    // redis 配置
    'redis' => [
        // 数默认连接池名
        'defaultPool'   => 'redis',
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
