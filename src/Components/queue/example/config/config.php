<?php

declare(strict_types=1);

use Imi\Util\Imi;

return [
    // 项目根命名空间
    'namespace'    => 'QueueApp',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    // 'beanScan'    => [
    //     'QueueApp\Consumer',
    //     'QueueApp\Listener',
    // ],

    // 组件命名空间
    'components'    => [
        'Queue'     => 'Imi\Queue',
        'Swoole'    => 'Imi\Swoole',
        'Workerman' => 'Imi\Workerman',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'     => 'QueueApp\HttpServer',
        'type'          => \Imi\Swoole\Server\Type::HTTP,
        'host'          => '127.0.0.1',
        'port'          => 8080,
        'configs'       => [
            'worker_num'        => 1,
        ],
    ],

    // Workerman 服务器配置
    'workermanServer' => [
        'http' => [
            'namespace' => 'QueueApp\HttpServer',
            'type'      => \Imi\Workerman\Server\Type::HTTP,
            'host'      => '127.0.0.1',
            'port'      => 8080,
            'configs'   => [
            ],
        ],
    ],

    // 连接池配置
    'pools'    => Imi::checkAppType('swoole') ? [
        'redis'    => [
            'pool' => [
                // 协程池类名
                'class'         => \Imi\Swoole\Redis\Pool\CoroutineRedisPool::class,
                'config'        => [
                    // 池子中最多资源数
                    'maxResources' => 10,
                    // 池子中最少资源数
                    'minResources' => 0,
                    // 资源回收时间间隔，单位：秒
                    // 'gcInterval' => 60,
                    // 获取资源最大存活时间，单位：秒
                    // 'maxActiveTime' => 3600,
                    // 等待资源最大超时时间，单位：毫秒
                    // 'waitTimeout' => 3000,
                    // 心跳时间间隔，单位：秒
                    // 'heartbeatInterval' => null,
                    // 当获取资源时，是否检查状态
                    // 'checkStateWhenGetResource' => true,
                    // 负载均衡-轮流
                    // 'resourceConfigMode' => ResourceConfigMode::TURN,
                    // 负载均衡-随机
                    // 'resourceConfigMode' => ResourceConfigMode::RANDOM,
                ],
            ],
            // 数组资源配置
            'resource' => [
                'host'    => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'    => 6379,
                // 是否自动序列化变量
                'serialize'    => false,
                // 密码
                'password'    => null,
                // 第几个库
                'db'    => 0,
            ],
            // uri资源配置，以分号;分隔多个，参数使用query参数格式，特殊字符需要转码
        ],
    ] : [],

    // 数据库配置
    'db'    => [
        // 数默认连接池名
        'defaultPool'    => 'maindb',
    ],

    // redis 配置
    'redis' => [
        // 数默认连接池名
        'defaultPool'   => 'redis',
        'connections'   => [
            'redis' => [
                'host'    => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'    => 6379,
                // 是否自动序列化变量
                'serialize'    => false,
                // 密码
                'password'    => null,
                // 第几个库
                'db'    => 0,
            ],
        ],
    ],

    // 锁
    'lock'  => [
        'list'  => [
            'redisConnectionContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
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
                            'filename' => \dirname(__DIR__) . '/.runtime/logs/log.log',
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
