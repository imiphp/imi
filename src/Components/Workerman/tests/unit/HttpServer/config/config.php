<?php

declare(strict_types=1);

return [
    // 项目根命名空间
    'namespace'    => 'Imi\Workerman\Test\HttpServer',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\HttpServer\Listener',
    ],

    // 组件命名空间
    'components'    => [
        'Workerman' => 'Imi\Workerman',
    ],

    // Workerman 服务器配置
    'workermanServer' => [
        'http' => [
            'namespace' => 'Imi\Workerman\Test\HttpServer\ApiServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => imiGetEnv('SERVER_HOST', '127.0.0.1'),
            'port'      => 13000,
            'configs'   => [
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

    // 锁
    'lock'  => [
        'list'  => [
            // 'atomic' =>  [
            //     'class' =>  'AtomicLock',
            //     'options'   =>  [
            //         'atomicName'    =>  'atomicLock',
            //     ],
            // ],
            'memoryTableLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
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
];
