<?php

declare(strict_types=1);

return [
    // 项目根命名空间
    'namespace'    => 'Imi\Workerman\Test\AppServer',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\AppServer\Listener',
        'Imi\Workerman\Test\AppServer\Cron',
    ],

    // 组件命名空间
    'components'    => [
        'Workerman' => 'Imi\Workerman',
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
                            'filename' => dirname(__DIR__) . '/logs/log.log',
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

    // Workerman 服务器配置
    'workermanServer' => [
        'http' => [
            'namespace' => 'Imi\Workerman\Test\AppServer\ApiServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'      => 13000,
            'configs'   => [
            ],
        ],
        'websocket' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\WebSocketServer',
            'type'        => Imi\Workerman\Server\Type::WEBSOCKET,
            'host'        => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'        => 13002,
            'shareWorker' => 'http',
            'configs'     => [
            ],
        ],
        'tcp' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\TcpServer',
            'type'        => Imi\Workerman\Server\Type::TCP,
            'host'        => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'        => 13003,
            'shareWorker' => 'http',
            'configs'     => [
                'protocol' => \Workerman\Protocols\Text::class,
            ],
            // 数据处理器
            'dataParser'    => \Imi\Workerman\Test\AppServer\TcpServer\Parser\JsonObjectParser::class,
        ],
        'udp' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\UdpServer',
            'type'        => Imi\Workerman\Server\Type::UDP,
            'host'        => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'        => 13004,
            'shareWorker' => 'http',
            'configs'     => [
            ],
            // 数据处理器
            'dataParser'    => Imi\Server\DataParser\JsonObjectParser::class,
        ],
    ],

    'workerman' => [
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
        'connections'   => [
            'redis' => [
                'host'        => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                'port'        => imiGetEnv('REDIS_SERVER_PORT', 6379),
                'password'    => imiGetEnv('REDIS_SERVER_PASSWORD'),
            ],
        ],
    ],

    // 锁
    'lock'  => [
        'default' => 'redisConnectionContextLock',
        'list'    => [
            'redisConnectionContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
    ],

    'imi' => [
        'beans' => [
            'ServerUtil' => \Imi\Workerman\Server\Util\LocalServerUtil::class,
        ],
    ],
];
