<?php

declare(strict_types=1);

use function Imi\env;

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\AppServer\TcpServer\Controller',
        'Imi\Workerman\Test\AppServer\TcpServer\Error',
        'Imi\Workerman\Test\AppServer\TcpServer\Middleware',
    ],
    'beans'    => [
        'TcpDispatcher'    => [
            'middlewares'    => [
                \Imi\Workerman\Test\AppServer\TcpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\TcpServer\Middleware\RouteMiddleware::class,
                \Imi\Workerman\Test\AppServer\TcpServer\Middleware\Test::class,
            ],
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'key'          => 'IMITEST.TCP.GROUP',
            'redisDb'      => 2,
        ],
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextLocal',
            'ttl'           => 600,
        ],
        'TcpRouteNotFoundHandler'   => [
            'handler'   => 'TcpRouteNotFound',
        ],
    ],
    // db 配置
    'db' => [
        // 默认连接池名
        'defaultPool' => 'maindb',
        'connections' => [
            'tradition' => [
                'dbClass'  => 'PdoMysqlDriver',
                'host'     => env('MYSQL_SERVER_HOST', '127.0.0.1'),
                'port'     => env('MYSQL_SERVER_PORT', 3306),
                'username' => env('MYSQL_SERVER_USERNAME', 'root'),
                'password' => env('MYSQL_SERVER_PASSWORD', 'root'),
                'database' => 'db_imi_test',
                'charset'  => 'utf8mb4',
            ],
        ],
    ],
    // 锁配置
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
];
