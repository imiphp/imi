<?php

declare(strict_types=1);

return [
    // 项目根命名空间
    'namespace'    => 'Imi\Fpm\Test\Web',

    'debug'       => false,

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'Imi\Fpm\Test\Web\Controller',
        'Imi\Fpm\Test\Web\Middleware',
        'Imi\Fpm\Test\Web\Error',
    ],

    // 组件命名空间
    'components'    => [
        'Fpm' => 'Imi\Fpm',
    ],

    'ignoreNamespace'   => [
        'Imi\Fpm\Test\Web\public\*',
        'Imi\Swoole\*',
        'Imi\Workerman\*',
        'Imi\Cron\*',
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

    'middleware'    => [
        'groups'    => [
            'test'  => [
                \Imi\Fpm\Test\Web\Middleware\Middleware4::class,
            ],
        ],
    ],
];
