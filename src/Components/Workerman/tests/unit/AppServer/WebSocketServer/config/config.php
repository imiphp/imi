<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Workerman\Test\AppServer\WebSocketServer\Controller',
        'Imi\Workerman\Test\AppServer\WebSocketServer\Error',
        'Imi\Workerman\Test\AppServer\WebSocketServer\Middleware',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Swoole\Test\WebSocketServer\MainServer\Middleware\Test::class,
            ],
        ],
        'GroupRedis'    => [
            'redisPool'    => 'redis',
            'redisDb'      => 2,
        ],
        'ConnectContextStore'   => [
            'handlerClass'  => 'ConnectContextLocal',
            'ttl'           => 600,
        ],
        'ConnectContextMemoryTable' => [
            'tableName' => 'connectContext',
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'RouteNotFound',
        ],
        'ConnectionBinder'  => [
            'redisPool' => 'redis',
            'key'       => 'imi:wsTest:connectionBinder:map',
        ],
    ],
    // db 配置
    'db' => [
        // 数默认连接池名
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
