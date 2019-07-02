<?php
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'Imi\Test\Component\Tests',
        'Imi\Test\Component\Aop',
        'Imi\Test\Component\Inject',
        'Imi\Test\Component\Event',
        'Imi\Test\Component\Enum',
        'Imi\Test\Component\Redis',
        'Imi\Test\Component\Db',
    ],
    'beans'    =>    [
    ],
    'imi'   =>  'very six',
    'yurun' =>  '',
    
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
                    'username'    => testEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => testEnv('MYSQL_SERVER_PASSWORD', 'root'),
                    'database'    => 'db_imi_test',
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
                    'username'    => testEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => testEnv('MYSQL_SERVER_PASSWORD', 'root'),
                    'database'    => 'db_imi_test',
                    'charset'     => 'utf8mb4',
                ],
            ]
        ],
        // 主数据库
        'maindb.slave'    =>    [
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
                    'username'    => testEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => testEnv('MYSQL_SERVER_PASSWORD', 'root'),
                    'database'    => 'db_imi_test',
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
                    'username'    => testEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => testEnv('MYSQL_SERVER_PASSWORD', 'root'),
                    'database'    => 'db_imi_test',
                    'charset'     => 'utf8mb4',
                ],
            ]
        ],
        'redis_test'    =>    [
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
    ],
    // db 配置
    'db' =>  [
        // 数默认连接池名
        'defaultPool'   =>  'maindb',
    ],
    // redis 配置
    'redis' =>  [
        // 数默认连接池名
        'defaultPool'   =>  'redis_test',
    ],
];