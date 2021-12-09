<?php

declare(strict_types=1);

use function Imi\env;

return [
    'components'    => [
        // 引入本组件
        'pgsql'    => 'Imi\Pgsql',
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

    // 连接池配置
    'pools'    => [
        // 主数据库
        'maindb'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\SyncDbPool::class,
                // 连接池配置
                'config'        => [
                    'maxResources'              => 10,
                    'minResources'              => 1,
                    'checkStateWhenGetResource' => false,
                ],
            ],
            // 连接池资源配置
            'resource'    => [
                'dbClass'     => 'PdoPgsqlDriver',
                'host'        => env('PGSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => env('PGSQL_SERVER_PORT', 5432),
                'username'    => env('PGSQL_SERVER_USERNAME', 'root'),
                'password'    => env('PGSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'db_imi_test',
            ],
        ],
        // 主数据库
        'maindb.slave'    => [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Db\Pool\SyncDbPool::class,
                // 连接池配置
                'config'        => [
                    'maxResources'              => 10,
                    'minResources'              => 1,
                    'checkStateWhenGetResource' => false,
                ],
            ],
            // 连接池资源配置
            'resource'    => [
                'dbClass'     => 'PdoPgsqlDriver',
                'host'        => env('PGSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => env('PGSQL_SERVER_PORT', 5432),
                'username'    => env('PGSQL_SERVER_USERNAME', 'root'),
                'password'    => env('PGSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'db_imi_test',
            ],
        ],
        'swoole'    => \extension_loaded('swoole_postgresql') ? [
            'pool'    => [
                // 协程池类名
                'class'    => \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
                // 连接池配置
                'config'        => [
                    'maxResources'              => 10,
                    'minResources'              => 1,
                    'checkStateWhenGetResource' => false,
                ],
            ],
            // 连接池资源配置
            'resource'    => [
                'dbClass'     => 'SwoolePgsqlDriver',
                'host'        => env('PGSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => env('PGSQL_SERVER_PORT', 5432),
                'username'    => env('PGSQL_SERVER_USERNAME', 'root'),
                'password'    => env('PGSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'db_imi_test',
            ],
        ] : [],
    ],
    // db 配置
    'db' => [
        // 默认连接池名
        'defaultPool' => 'maindb',
        'connections' => [
            'maindb' => [
                'dbClass'     => 'PdoPgsqlDriver',
                'host'        => env('PGSQL_SERVER_HOST', '127.0.0.1'),
                'port'        => env('PGSQL_SERVER_PORT', 5432),
                'username'    => env('PGSQL_SERVER_USERNAME', 'root'),
                'password'    => env('PGSQL_SERVER_PASSWORD', 'root'),
                'database'    => 'db_imi_test',
            ],
        ],
    ],
];
