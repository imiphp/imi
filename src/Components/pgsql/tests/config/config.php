<?php

declare(strict_types=1);

use function Imi\env;

return [
    'components'    => [
        // 引入本组件
        'pgsql'    => 'Imi\Pgsql',
    ],

    // 日志配置
    'logger'        => [
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

    'connectionCenter' => [
        // 主数据库
        'maindb' => [
            'manager' => \Imi\ConnectionCenter\Handler\RequestContextSingleton\RequestContextSingletonConnectionManager::class,
            'config'  => [
                'driver'    => \Imi\Db\ConnectionCenter\DatabaseDriver::class,
                'dbDriver'  => \Imi\Pgsql\Db\Drivers\PdoPgsql\Driver::class,
                'resources' => [
                    [
                        'host'        => env('PGSQL_SERVER_HOST', '127.0.0.1'),
                        'port'        => env('PGSQL_SERVER_PORT', 5432),
                        'username'    => env('PGSQL_SERVER_USERNAME', 'root'),
                        'password'    => env('PGSQL_SERVER_PASSWORD', 'root'),
                        'database'    => 'db_imi_test',
                    ],
                ],
            ],
        ],
        // 从数据库
        'maindb.slave'    => [
            'manager' => \Imi\ConnectionCenter\Handler\RequestContextSingleton\RequestContextSingletonConnectionManager::class,
            'config'  => [
                'driver'    => \Imi\Db\ConnectionCenter\DatabaseDriver::class,
                'dbDriver'  => \Imi\Pgsql\Db\Drivers\PdoPgsql\Driver::class,
                'resources' => [
                    [
                        'host'        => env('PGSQL_SERVER_HOST', '127.0.0.1'),
                        'port'        => env('PGSQL_SERVER_PORT', 5432),
                        'username'    => env('PGSQL_SERVER_USERNAME', 'root'),
                        'password'    => env('PGSQL_SERVER_PASSWORD', 'root'),
                        'database'    => 'db_imi_test',
                    ],
                ],
            ],
        ],
        ...(class_exists(\Swoole\Coroutine\PostgreSQL::class, false) ? [
            'swoole'    => [
                'manager' => \Imi\ConnectionCenter\Handler\Pool\PoolConnectionManager::class,
                'config'  => [
                    'driver'    => \Imi\Db\ConnectionCenter\DatabaseDriver::class,
                    'dbDriver'  => \Imi\Pgsql\Db\Drivers\Swoole\Driver::class,
                    'resources' => [
                        [
                            'host'        => env('PGSQL_SERVER_HOST', '127.0.0.1'),
                            'port'        => env('PGSQL_SERVER_PORT', 5432),
                            'username'    => env('PGSQL_SERVER_USERNAME', 'root'),
                            'password'    => env('PGSQL_SERVER_PASSWORD', 'root'),
                            'database'    => 'db_imi_test',
                        ],
                    ],
                ],
            ],
        ] : []),
    ],
    // db 配置
    'db'            => [
        // 默认连接池名
        'defaultPool' => 'maindb',
    ],
];
