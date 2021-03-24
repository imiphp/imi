<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\AppServer\UdpServer\Controller',
        'Imi\Workerman\Test\AppServer\UdpServer\Error',
        'Imi\Workerman\Test\AppServer\UdpServer\Middleware',
    ],
    'beans'    => [
        'UdpDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\UdpServer\Middleware\RouteMiddleware::class,
                \Imi\Workerman\Test\AppServer\UdpServer\Middleware\Test::class,
            ],
        ],
        'UdpRouteNotFoundHandler'   => [
            'handler'   => 'UdpRouteNotFound',
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
    // 锁配置
    'lock'  => [
        'default' => 'redisConnectContextLock',
        'list'    => [
            'redisConnectContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
    ],
];
