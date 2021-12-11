<?php

declare(strict_types=1);

use function Imi\env;

return [
    // 项目根命名空间
    'namespace'    => 'Imi\Swoole\Test\HttpServer',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\HttpServer\Listener',
        'Imi\Swoole\Test\HttpServer\Task',
        'Imi\Swoole\Test\HttpServer\Process',
        'Imi\Swoole\Test\HttpServer\Cron',
        'Imi\Swoole\Test\HttpServer\Listener',
    ],

    // 组件命名空间
    'components'    => [
        'Swoole' => 'Imi\Swoole',
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
                            'filename' => \dirname(__DIR__) . '/logs/log.log',
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

    // 主服务器配置
    'mainServer'    => [
        'namespace'    => 'Imi\Swoole\Test\HttpServer\ApiServer',
        'type'         => Imi\Swoole\Server\Type::HTTP,
        'host'         => env('SERVER_HOST', '127.0.0.1'),
        'port'         => 13000,
        'mode'         => \SWOOLE_BASE,
        'configs'      => [
            'worker_num'        => 2,
            'task_worker_num'   => 1,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'    => [
        'SessionTest'   => [
            'namespace' => 'Imi\Swoole\Test\HttpServer\SessionTestServer',
            'type'      => Imi\Swoole\Server\Type::HTTP,
            'host'      => env('SERVER_HOST', '127.0.0.1'),
            'port'      => 13005,
        ],
        'HttpsTest'     => [
            'namespace' => 'Imi\Swoole\Test\HttpServer\HttpsTestServer',
            'type'      => Imi\Swoole\Server\Type::HTTP,
            'host'      => env('SERVER_HOST', '127.0.0.1'),
            'port'      => 13006,
            'sockType'  => \SWOOLE_SOCK_TCP | \SWOOLE_SSL,
            'configs'   => [
                'ssl_cert_file'     => \dirname(__DIR__, 7) . '/tests/ssl/server.crt',
                'ssl_key_file'      => \dirname(__DIR__, 7) . '/tests/ssl/server.key',
            ],
        ],
        'Http2Test'   => [
            'namespace' => 'Imi\Swoole\Test\HttpServer\Http2TestServer',
            'type'      => Imi\Swoole\Server\Type::HTTP,
            'host'      => env('SERVER_HOST', '127.0.0.1'),
            'port'      => 13007,
            'sockType'  => \SWOOLE_SOCK_TCP | \SWOOLE_SSL,
            'configs'   => [
                'open_http2_protocol'   => true,
                'ssl_cert_file'         => \dirname(__DIR__, 7) . '/tests/ssl/server.crt',
                'ssl_key_file'          => \dirname(__DIR__, 7) . '/tests/ssl/server.key',
            ],
        ],
    ],

    // 连接池配置
    'pools'    => [
        // 主数据库
        'maindb'    => [
            'pool'    => [
                'class'        => \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
                'config'       => [
                    'maxResources'    => 10,
                    'minResources'    => 1,
                ],
            ],
            'resource'    => [
                'host'        => env('MYSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => env('MYSQL_SERVER_PORT', 3306),
                'username'    => env('MYSQL_SERVER_USERNAME', 'root'),
                'password'    => env('MYSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'mysql',
                'charset'     => 'utf8mb4',
            ],
        ],
        'redis'    => [
            'pool'    => [
                'class'        => \Imi\Swoole\Redis\Pool\CoroutineRedisPool::class,
                'config'       => [
                    'maxResources'    => 10,
                    'minResources'    => 1,
                ],
            ],
            'resource'    => [
                'host'      => env('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'      => env('REDIS_SERVER_PORT', 6379),
                'password'  => env('REDIS_SERVER_PASSWORD'),
            ],
        ],
    ],

    // 数据库配置
    'db'    => [
        // 默认连接池名
        'defaultPool'    => 'maindb',
    ],

    // redis 配置
    'redis' => [
        // 默认连接池名
        'defaultPool'   => 'redis',
    ],

    // 内存表配置
    'memoryTable'   => [
        't1'    => [
            'columns'   => [
                ['name' => 'name', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 16],
                ['name' => 'quantity', 'type' => \Swoole\Table::TYPE_INT],
            ],
            'lockId'    => 'memoryTableLock',
        ],
        'ConnectionContext'    => [
            'class'  => \Imi\Swoole\Server\ConnectionContext\StoreHandler\MemoryTable\ConnectionContextOption::class,
            'lockId' => 'redisConnectionContextLock',
        ],
    ],

    // 锁
    'lock'  => [
        'list'  => [
            // 'atomic' =>  [
            //     'class' =>  'AtomicLock',
            //     'options'   =>  [
            //         'atomicName'    =>  'atomicLock',
            //     ],
            // ],
            'memoryTableLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
            'redisConnectionContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
    ],

    // atmoic 配置
    'atomics'    => [
        'atomicLock'   => 1,
    ],
];
