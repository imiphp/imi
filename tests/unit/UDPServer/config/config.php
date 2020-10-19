<?php

return [
    // 项目根命名空间
    'namespace'    => 'Imi\Test\UDPServer',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'Imi\Test\UDPServer\Listener',
    ],

    // 组件命名空间
    'components'    => [
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'    => 'Imi\Test\UDPServer\MainServer',
        'type'         => Imi\Server\Type::UDP_SERVER,
        'host'         => imiGetEnv('SERVER_HOST', '127.0.0.1'),
        'port'         => 13004,
        'configs'      => [
            'worker_num'    => 1,
        ],
        // 数据处理器
        'dataParser'    => Imi\Server\DataParser\JsonObjectParser::class,
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
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
                ],
            ],
            'async'    => [
                'pool'    => [
                    'class'        => \Imi\Redis\CoroutineRedisPool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 0,
                    ],
                ],
                'resource'    => [
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
                ],
            ],
        ],
    ],

    // redis 配置
    'redis' => [
        // 数默认连接池名
        'defaultPool'   => 'redis',
    ],
];
