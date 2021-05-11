<?php

use Imi\Grpc\Client\GrpcClient;

return [
    // 项目根命名空间
    'namespace'    => 'GrpcApp',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'GrpcApp\Listener',
        'GrpcApp\Task',
    ],

    // 组件命名空间
    'components'    => [
        'Grpc'  => 'Imi\Grpc',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace' => 'GrpcApp\GrpcServer',
        'type'      => 'Grpc',
        'host'      => '127.0.0.1',
        'port'      => 8080,
        'configs'   => [
            'worker_num'        => 1,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
        'SubServerName'   => [
            'namespace'    => 'GrpcApp\ApiServer',
            'type'         => Imi\Server\Type::HTTP,
            'host'         => '127.0.0.1',
            'port'         => 8081,
        ],
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
                    'class'        => \Imi\Redis\CoroutineRedisPool::class,
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
        'grpc'  => [
            'async'    => [
                'pool'    => [
                    'class'        => \Imi\Rpc\Client\Pool\RpcClientCoroutinePool::class,
                    'config'       => [
                        'maxResources'  => 100,
                        'minResources'  => 1,
                    ],
                ],
                'resource'    => [
                    'url'           => 'http://127.0.0.1:8080/{package}.{service}/{name}',
                    'clientClass'   => GrpcClient::class,
                    'timeout'       => 30,
                ],
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
        'connectContext'    => [
            'class'  => \Imi\Server\ConnectContext\StoreHandler\MemoryTable\ConnectContextOption::class,
            'lockId' => 'redisConnectContextLock',
            'size'   => 65536,
        ],
    ],

    // 锁
    'lock'  => [
        'list'  => [
            'redisConnectContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
    ],

    'rpc'   => [
        'defaultPool'   => 'grpc',
    ],
];
