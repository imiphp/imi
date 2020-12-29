<?php

declare(strict_types=1);

use Imi\Log\LogLevel;
use Imi\Swoole\Context\CoroutineContextManager;

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
    ],
    'ignoreNamespace'   => [
        'Imi\Test\Component\Annotation\A\*',
        'Imi\Test\Component\Annotation\B\TestB',
    ],
    'beans'    => [
        'TestPropertyClass' => [
            'b' => 'bbb',
        ],
        'Logger'            => [
            'exHandlers'    => [
                // 指定级别日志输出trace
                [
                    'class'        => \Imi\Log\Handler\File::class,
                    'options'      => [
                        'levels'        => [
                            LogLevel::ALERT,
                            LogLevel::CRITICAL,
                            LogLevel::DEBUG,
                            LogLevel::EMERGENCY,
                            LogLevel::ERROR,
                            LogLevel::NOTICE,
                            LogLevel::WARNING,
                        ],
                        'fileName'      => dirname(__DIR__) . '/logs/{Y}-{m}-{d}.log',
                        'format'        => "{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}\n{trace}",
                        'traceFormat'   => '#{index}  {call} called at [{file}:{line}]',
                    ],
                ],
                [
                    'class'        => \Imi\Log\Handler\File::class,
                    'options'      => [
                        'levels'        => [
                            LogLevel::INFO,
                        ],
                        'fileName'      => dirname(__DIR__) . '/logs/{Y}-{m}-{d}.log',
                        'format'        => '{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}',
                    ],
                ],
                [
                    'class'     => \Imi\Log\Handler\Console::class,
                    'options'   => [
                        'levels'        => [
                            'Test',
                        ],
                        'format'         => '{message}',
                        'logCacheNumber' => 10240,
                    ],
                ],
            ],
        ],
        'ErrorLog'          => [
            // 'level' =>  ,
        ],
        'DbQueryLog' => [
            'enable' => true,
        ],
    ],
    'imi'   => [
        'RequestContext' => CoroutineContextManager::class,
    ],
    'imi-framework'   => 'very six',
    'yurun'           => getenv('APP_YURUN') ?: '',

    // 连接池配置
    'pools'    => [
        // 主数据库
        'maindb'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\CoroutineDbPool::class,
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
                'class'    => \Imi\Db\Pool\CoroutineDbPool::class,
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
        'swooleMysql'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\CoroutineDbPool::class,
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
                'dbClass'     => \Imi\Db\Drivers\Swoole\Driver::class,
            ],
        ],
        // mysqli
        'mysqli'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\CoroutineDbPool::class,
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
                'dbClass'     => \Imi\Db\Drivers\Mysqli\Driver::class,
            ],
        ],
        'redis_test'    => [
            'pool'    => [
                'class'        => \Imi\Redis\CoroutineRedisPool::class,
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
                'class'        => \Imi\Redis\CoroutineRedisPool::class,
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
                'class'        => \Imi\Redis\CoroutineRedisPool::class,
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
        // 数默认连接池名
        'defaultPool'   => 'maindb',
    ],
    // redis 配置
    'redis' => [
        // 数默认连接池名
        'defaultPool'   => 'redis_test',
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
                'savePath'              => dirname(__DIR__) . '/.runtime/cache/',
                'formatHandlerClass'    => \Imi\Util\Format\Json::class,
            ],
        ],
        'file2'  => [
            'handlerClass'  => \Imi\Cache\Handler\File::class,
            'option'        => [
                'savePath'    => dirname(__DIR__) . '/.runtime/cache/',
                // 保存文件名处理回调，一般可以不写
                'saveFileNameCallback'    => function ($savePath, $key) {
                    return \Imi\Util\File::path($savePath, sha1($key));
                },
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
    ],
    // atmoic 配置
    'atomics'    => [
        'atomicLock'   => 1,
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
            'atomic' => [
                'class'     => 'AtomicLock',
                'options'   => [
                    'atomicName'    => 'atomicLock',
                ],
            ],
        ],
    ],
    'yurun2'   => imiGetEnv('yurun'),
    'tools'    => [
        'generate/model'    => [
            'namespace' => [
                'Imi\Test\Component\Model' => [
                    'tables'    => [
                        'tb_tree',
                    ],
                    'withRecords'   => [
                        'tb_tree',
                    ],
                ],
            ],
        ],
    ],
];
