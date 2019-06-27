<?php
return [
    // 项目根命名空间
    'namespace'    =>    'Imi\Test\HttpServer',

    // 配置文件
    'configs'    =>    [
        'beans'        =>    __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    =>    [
        'Imi\Test\HttpServer\Listener',
    ],

    // 组件命名空间
    'components'    =>  [
    ],

    // 主服务器配置
    'mainServer'    =>    [
        'namespace'    =>    'Imi\Test\HttpServer\ApiServer',
        'type'        =>    Imi\Server\Type::HTTP,
        'host'        =>    '127.0.0.1',
        'port'        =>    13000,
        'configs'    =>    [
            
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        =>    [
    ],

    // 连接池配置
    'pools'    =>    [
        // 主数据库
        'maindb'    =>    [
            // 同步池子
            'sync'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Db\Pool\SyncDbPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'        => '127.0.0.1',
                    'username'    => 'root',
                    'password'    => 'root',
                    'database'    => 'mysql',
                    'charset'     => 'utf8mb4',
                ],
            ],
            // 异步池子，worker进程使用
            'async'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Db\Pool\CoroutineDbPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'        => '127.0.0.1',
                    'username'    => 'root',
                    'password'    => 'root',
                    'database'    => 'mysql',
                    'charset'     => 'utf8mb4',
                ],
            ]
        ],
        'redis'    =>    [
            'sync'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Redis\SyncRedisPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'        => '127.0.0.1',
                    'port'        =>    6379,
                ]
            ],
            'async'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Redis\CoroutineRedisPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'        => '127.0.0.1',
                    'port'        =>    6379,
                ]
            ],
        ],
        'redisSession'    =>    [
            'sync'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Redis\SyncRedisPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'          => '127.0.0.1',
                    'port'          => 6379,
                    'serialize'     => false,
                ]
            ],
            'async'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Redis\CoroutineRedisPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'          => '127.0.0.1',
                    'port'          => 6379,
                    'serialize'     => false,
                ]
            ],
        ],
    ],

    // 数据库配置
    'db'    =>    [
        // 数默认连接池名
        'defaultPool'    =>    'maindb',
    ],

    // redis 配置
    'redis' =>  [
        // 数默认连接池名
        'defaultPool'   =>  'redis',
    ],
];