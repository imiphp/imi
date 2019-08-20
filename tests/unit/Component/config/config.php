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
        'Imi\Test\Component\Cache',
        'Imi\Test\Component\Lock',
        'Imi\Test\Component\Model',
        'Imi\Test\Component\Validate',
        'Imi\Test\Component\Util\Imi',
    ],
    'beans'    =>    [
        'TestPropertyClass' =>  [
            'b' =>  'bbb',
        ],
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
                    'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                    'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                    'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
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
                    'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                    'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                    'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
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
                    'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                    'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                    'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
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
                    'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                    'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                    'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                    'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
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
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
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
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
                ]
            ],
        ],
        'redis_cache'    =>    [
            'sync'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Redis\SyncRedisPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
                    'serialize'   =>    false,
                    'db'          =>    imiGetEnv('REDIS_CACHE_DB', 1),
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
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
                    'serialize'   =>    false,
                    'db'          =>    1,
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
    // 缓存配置
    'cache' =>  [
        'default'   =>  'file1',
    ],
    // 缓存
    'caches'    =>  [
        'file1'  =>  [
            'handlerClass'  =>  \Imi\Cache\Handler\File::class,
            'option'    =>  [
                'savePath'    =>    dirname(__DIR__) . '/.runtime/cache/',
                'formatHandlerClass'    =>  \Imi\Util\Format\Json::class,
            ],
        ],
        'file2'  =>  [
            'handlerClass'  =>  \Imi\Cache\Handler\File::class,
            'option'    =>  [
                'savePath'    =>    dirname(__DIR__) . '/.runtime/cache/',
                // 保存文件名处理回调，一般可以不写
                'saveFileNameCallback'    =>    function($savePath, $key){
                    return \Imi\Util\File::path($savePath, sha1($key));
                },
                'formatHandlerClass'    =>  \Imi\Util\Format\Json::class,
            ],
        ],
        'redis' =>  [
            'handlerClass'  =>    \Imi\Cache\Handler\Redis::class,
            'option'        =>    [
                'poolName'    =>    'redis_cache',
                'formatHandlerClass'    =>  \Imi\Util\Format\Json::class,
            ],
        ],
        'redisHash' =>  [
            'handlerClass'  =>    \Imi\Cache\Handler\RedisHash::class,
            'option'        =>    [
                'poolName'  =>    'redis_cache',
                'separator' =>    '->',
                'formatHandlerClass'    =>  \Imi\Util\Format\Json::class,
            ],
        ],
    ],
    // atmoic 配置
    'atomics'    =>  [
        'atomicLock'   =>  1,
        'test'
    ],
    // 锁
    'lock'  =>[
        'list'  =>  [
            'redis' =>  [
                'class' =>  'RedisLock',
                'options'   =>  [
                    'poolName'  =>  'redis_test',
                ],
            ],
            'atomic' =>  [
                'class' =>  'AtomicLock',
                'options'   =>  [
                    'atomicName'    =>  'atomicLock',
                ],
            ],
        ],
    ],
];