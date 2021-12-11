<?php

declare(strict_types=1);

use function Imi\env;

// 注释项代表可省略的，使用默认值
return [
    // 项目根命名空间
    'namespace'    => 'Imi\AC\Test',
    // 扫描目录
    'beanScan'    => [
        'Imi\AC\Test\Tool',
    ],
    // 组件命名空间
    'components'    => [
        'AccessControl'       => 'Imi\AC',
    ],
    // 主服务器配置
    'mainServer'    => [
    ],
    // 子服务器（端口监听）配置
    'subServers'        => [
    ],
    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],
    'db'    => [
        // 数据库默认连接池名
        'defaultPool'    => 'maindb',
    ],
    'pools'    => [
        // 数据库连接池名：maindb
        'maindb'    => [
            'pool'    => [
                'class'        => \Imi\Db\Pool\SyncDbPool::class,
                'config'       => [
                    'maxResources'    => 10,
                    'minResources'    => 0,
                ],
            ],
            'resource'    => [
                'dbClass'     => 'PdoMysqlDriver',
                'host'        => env('MYSQL_SERVER_HOST', '127.0.0.1'),
                'username'    => 'root',
                'password'    => 'root',
                'database'    => 'db_imi_access_control',
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
                            'filename' => \dirname(__DIR__, 2) . '/log.log',
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
