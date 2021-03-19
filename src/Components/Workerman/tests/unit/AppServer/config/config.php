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
        ],
        'udp' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\UdpServer',
            'type'        => Imi\Workerman\Server\Type::UDP,
            'host'        => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'        => 13004,
            'shareWorker' => 'http',
            'configs'     => [
            ],
        ],
        'channel' => [
            'namespace'   => '',
            'type'        => Imi\Workerman\Server\Type::CHANNEL,
            'host'        => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'        => 13005,
            'configs'     => [
            ],
        ],
        'httpWorker2' => [
            'namespace' => 'Imi\Workerman\Test\AppServer\ApiServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'      => 13006,
            'configs'   => [
                'count' => 2,
            ],
        ],
        'websocketWorker2' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\WebSocketServer',
            'type'        => Imi\Workerman\Server\Type::WEBSOCKET,
            'host'        => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'        => 13007,
            'shareWorker' => 'httpWorker2',
        ],
    ],

    'workerman' => [
        // 多进程通讯组件配置
        'channel' => [
            'host' => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port' => 13005,
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
        'default' => 'redisConnectContextLock',
        'list'    => [
            // 'atomic' =>  [
            //     'class' =>  'AtomicLock',
            //     'options'   =>  [
            //         'atomicName'    =>  'atomicLock',
            //     ],
            // ],
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

    'imi' => [
        'ServerUtil' => \Imi\Workerman\Server\Util\LocalServerUtil::class,
    ],
];
