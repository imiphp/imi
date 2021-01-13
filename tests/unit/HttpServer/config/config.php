<?php

declare(strict_types=1);

return [
    // 项目根命名空间
    'namespace'    => 'Imi\Test\HttpServer',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // imi 核心配置
    'imi'   => [
        'RequestContext'    => \Imi\Swoole\Context\CoroutineContextManager::class,
    ],

    // 扫描目录
    'beanScan'    => [
        'Imi\Test\HttpServer\Listener',
        'Imi\Test\HttpServer\Task',
        'Imi\Test\HttpServer\Process',
        'Imi\Test\HttpServer\Cron',
        'Imi\Test\HttpServer\Listener',
    ],

    // 组件命名空间
    'components'    => [
        'Swoole' => 'Imi\Swoole',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'    => 'Imi\Test\HttpServer\ApiServer',
        'type'         => Imi\Swoole\Server\Type::HTTP,
        'host'         => imiGetEnv('SERVER_HOST', '127.0.0.1'),
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
            'namespace' => 'Imi\Test\HttpServer\SessionTestServer',
            'type'      => Imi\Swoole\Server\Type::HTTP,
            'host'      => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'      => 13005,
        ],
        'HttpsTest'     => [
            'namespace' => 'Imi\Test\HttpServer\HttpsTestServer',
            'type'      => Imi\Swoole\Server\Type::HTTP,
            'host'      => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'      => 13006,
            'sockType'  => \SWOOLE_SOCK_TCP | \SWOOLE_SSL,
            'configs'   => [
                'ssl_cert_file'     => dirname(__DIR__, 3) . '/ssl/server.crt',
                'ssl_key_file'      => dirname(__DIR__, 3) . '/ssl/server.key',
            ],
        ],
        'Http2Test'   => [
            'namespace' => 'Imi\Test\HttpServer\Http2TestServer',
            'type'      => Imi\Swoole\Server\Type::HTTP,
            'host'      => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'      => 13007,
            'sockType'  => \SWOOLE_SOCK_TCP | \SWOOLE_SSL,
            'configs'   => [
                'open_http2_protocol'   => true,
                'ssl_cert_file'         => dirname(__DIR__, 3) . '/ssl/server.crt',
                'ssl_key_file'          => dirname(__DIR__, 3) . '/ssl/server.key',
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
                'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
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
                'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
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
            'lockId'    => 'memoryTableLock',
        ],
        'connectContext'    => [
            'class'  => \Imi\Swoole\Server\ConnectContext\StoreHandler\MemoryTable\ConnectContextOption::class,
            'lockId' => 'redisConnectContextLock',
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
            'redisConnectContextLock' => [
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
