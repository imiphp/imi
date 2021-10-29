<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
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
        'Imi\Test\Component\Inherit',
        'Imi\Test\Component\Util\Imi',
        'Imi\Test\Component\Facade',
        'Imi\Test\Component\Annotation',
        'Imi\Test\Component\Partial',
        'Imi\Test\Component\Tool',
        'Imi\Test\Component\RequestContextProxy',
        'Imi\Test\Component\Bean',
        'Imi\Test\Component\Async',
    ],
    'ignoreNamespace'   => [
        'Imi\Test\Component\Annotation\A\*',
        'Imi\Test\Component\Annotation\B\TestB',
    ],

    // 组件命名空间
    'components'    => [
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

    'beans'    => [
        'TestPropertyClass' => [
            'b' => 'bbb',
        ],
        'ErrorLog'          => [
            'exceptionLevel' => \E_ALL,
        ],
        'DbQueryLog' => [
            'enable' => true,
        ],
    ],
    'imi-framework'   => 'very six',

    // 连接池配置
    'pools'    => [
        // 主数据库
        'maindb'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\SyncDbPool::class,
                // 连接池配置
                'config'        => [
                    'maxResources'              => 10,
                    'minResources'              => 1,
                    'checkStateWhenGetResource' => false,
                ],
            ],
            // 连接池资源配置
            'resource'    => [
                'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'db_imi_test',
                'charset'     => 'utf8mb4',
            ],
        ],
        // 主数据库
        'maindb.slave'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\SyncDbPool::class,
                // 连接池配置
                'config'        => [
                    'maxResources'              => 10,
                    'minResources'              => 1,
                    'checkStateWhenGetResource' => false,
                ],
            ],
            // 连接池资源配置
            'resource'    => [
                'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'db_imi_test',
                'charset'     => 'utf8mb4',
            ],
        ],
        // mysqli
        'mysqli'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\SyncDbPool::class,
                // 连接池配置
                'config'        => [
                    'maxResources'              => 10,
                    'minResources'              => 1,
                    'checkStateWhenGetResource' => false,
                ],
            ],
            // 连接池资源配置
            'resource'    => [
                'host'        => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                'username'    => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                'password'    => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'db_imi_test',
                'charset'     => 'utf8mb4',
                'dbClass'     => 'MysqliDriver',
            ],
        ],
        'redis_test'    => [
            'pool'    => [
                'class'        => \Imi\Redis\SyncRedisPool::class,
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
        'redis_cache'    => [
            'pool'    => [
                'class'        => \Imi\Redis\SyncRedisPool::class,
                'config'       => [
                    'maxResources'    => 10,
                    'minResources'    => 1,
                ],
            ],
            'resource'    => [
                'host'        => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('REDIS_SERVER_PORT', 6379),
                'password'    => imiGetEnv('REDIS_SERVER_PASSWORD'),
                'serialize'   => false,
                'db'          => 1,
            ],
        ],
        'redis_manager_test'    => [
            'pool'    => [
                'class'        => \Imi\Redis\SyncRedisPool::class,
                'config'       => [
                    'maxResources'    => 10,
                    'minResources'    => 1,
                ],
            ],
            'resource'    => [
                'host'        => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('REDIS_SERVER_PORT', 6379),
                'password'    => imiGetEnv('REDIS_SERVER_PASSWORD'),
                'serialize'   => false,
                'db'          => 1,
            ],
        ],
    ],
    // db 配置
    'db' => [
        // 默认连接池名
        'defaultPool' => 'maindb',
        'connections' => [
            'tradition' => [
                'dbClass'  => 'PdoMysqlDriver',
                'host'     => imiGetEnv('MYSQL_SERVER_HOST', '127.0.0.1'),
                'port'     => imiGetEnv('MYSQL_SERVER_PORT', 3306),
                'username' => imiGetEnv('MYSQL_SERVER_USERNAME', 'root'),
                'password' => imiGetEnv('MYSQL_SERVER_PASSWORD', 'root'),
                'database' => 'db_imi_test',
                'charset'  => 'utf8mb4',
            ],
        ],
    ],
    // redis 配置
    'redis' => [
        // 默认连接池名
        'defaultPool'   => 'redis_test',
        'connections'   => [
            'tradition' => [
                'host'        => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('REDIS_SERVER_PORT', 6379),
                'password'    => imiGetEnv('REDIS_SERVER_PASSWORD'),
                'serialize'   => false,
            ],
        ],
    ],
    // 缓存配置
    'cache' => [
        'default'   => 'file1',
    ],
    // 缓存
    'caches'    => [
        'file1'  => [
            'handlerClass'  => \Imi\Cache\Handler\File::class,
            'option'        => [
                'savePath'              => \dirname(__DIR__) . '/.runtime/cache/',
                'formatHandlerClass'    => \Imi\Util\Format\Json::class,
            ],
        ],
        'file2'  => [
            'handlerClass'  => \Imi\Cache\Handler\File::class,
            'option'        => [
                'savePath'    => \dirname(__DIR__) . '/.runtime/cache/',
                // 保存文件名处理回调，一般可以不写
                'saveFileNameCallback'    => function (string $savePath, string $key) {
                    return \Imi\Util\File::path($savePath, sha1($key));
                },
                'formatHandlerClass'    => \Imi\Util\Format\Json::class,
            ],
        ],
        'file3'  => [
            'handlerClass'  => \Imi\Cache\Handler\File::class,
            'option'        => [
                'savePath'              => \dirname(__DIR__) . '/.runtime/cache/',
                'formatHandlerClass'    => \Imi\Util\Format\Json::class,
            ],
        ],
        'redis' => [
            'handlerClass'  => \Imi\Cache\Handler\Redis::class,
            'option'        => [
                'poolName'              => 'redis_cache',
                'formatHandlerClass'    => \Imi\Util\Format\Json::class,
            ],
        ],
        'redisHash' => [
            'handlerClass'  => \Imi\Cache\Handler\RedisHash::class,
            'option'        => [
                'poolName'              => 'redis_cache',
                'separator'             => '->',
                'formatHandlerClass'    => \Imi\Util\Format\Json::class,
            ],
        ],
        'apcu' => [
            'handlerClass'  => \Imi\Cache\Handler\Apcu::class,
            'option'        => [
            ],
        ],
    ],
    // atmoic 配置
    'atomics'    => [
        'test',
    ],
    // 锁
    'lock'  => [
        'list'  => [
            'redis' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis_test',
                ],
            ],
        ],
    ],
    'yurun2'   => imiGetEnv('yurun'),
    'tools'    => [
        'generate/model'    => [
            'relation' => [
                'tb_test_list' => [
                    'fields' => [
                        'list' => [
                            'typeDefinition' => false,
                        ],
                    ],
                ],
            ],
            'namespace' => [
                'Imi\Test\Component\Model' => [
                    'tables'    => [
                        'tb_tree',
                    ],
                    'withRecords'   => [
                        'tb_tree',
                        'tb_role',
                    ],
                ],
            ],
        ],
    ],
];
