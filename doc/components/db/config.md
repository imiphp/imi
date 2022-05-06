# 连接池配置

[toc]

数据库连接池配置方式已经在连接池里讲过，这里就不重复了，直接说使用方法。

> 连接池适用于 Swoole 环境

标准写法：

> 从 imi v1.2.1 版本开始支持

```php
<?php
return [
    'db'    => [
        'defaultPool'   => 'alias1', // 默认连接池
        'statement'     =>  [
            'cache' =>  true, // 是否开启 statement 缓存，默认开启
        ],
    ],
    'pools' => [
        // 连接池名称
        'alias1' => [
            'pool' => [
                // 协程池类名
                'class'    =>    \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
                // 同步池类名，一般用不着
                // 'class'     =>    \Imi\Db\Pool\SyncDbPool::class,
                // 连接池配置
                'config' => [
                    // 池子中最多资源数
                    // 'maxResources' => 10,
                    // 池子中最少资源数
                    // 'minResources' => 2,
                    // 资源回收时间间隔，单位：秒
                    // 'gcInterval' => 60,
                    // 获取资源最大存活时间，单位：秒
                    // 'maxActiveTime' => 3600,
                    // 等待资源最大超时时间，单位：毫秒
                    // 'waitTimeout' => 3000,
                    // 心跳时间间隔，单位：秒
                    // 'heartbeatInterval' => null,
                    // 当获取资源时，是否检查状态
                    // 'checkStateWhenGetResource' => true,
                    // 每次获取资源最长使用时间，单位：秒；为 null 则不限制
                    // 'maxUsedTime' => null,
                    // 资源创建后最大空闲回收时间，单位：秒；为 null 则不限制
                    // 'maxIdleTime' => null,
                    // 当前请求上下文资源检查状态间隔，单位：支持小数的秒；为 null 则不限制
                    // 'requestResourceCheckInterval' => 30,
                    // 负载均衡-轮流
                    // 'resourceConfigMode' => ResourceConfigMode::TURN,
                    // 负载均衡-随机
                    // 'resourceConfigMode' => ResourceConfigMode::RANDOM,
                ],
            ],
            // 连接池资源配置
            'resource' => [
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => 'root',
                'database' => 'database',
                'prefix'   => '', // 表前缀
                // 'port'    => '3306',
                // 'timeout' => '建立连接超时时间',
                // 'charset' => '',
                // 使用 hook pdo 驱动（缺省默认）
                // 'dbClass' => 'PdoMysqlDriver',
                // 使用 hook mysqli 驱动
                // 'dbClass' => 'MysqliDriver',
                // 使用 Swoole MySQL 驱动
                // 'dbClass' => 'SwooleMysqlDriver',
                // 数据库连接后，执行初始化的 SQL
                // 'initSqls' => [
                //     'select 1',
                //     'select 2',
                // ],
            ],
            // uri 写法
            // 'resource'  =>  [
            //     'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
            //     'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
            // ],
            // 'resource'  =>  'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60;tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
        ],
        // 从库配置
        // 原连接池名后加.slave即为从库配置，非必设
        // 如果配置了，默认查询走从库，增删改走主库
        // 如果在事务中，默认都走主库
        'alias1.slave' => [
            'pool' => [
                // 协程池类名
                'class'    =>    \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
                // 同步池类名，一般用不着
                // 'class'     =>    \Imi\Db\Pool\SyncDbPool::class,
                // 连接池配置
                'config' => [
                    // 池子中最多资源数
                    // 'maxResources' => 10,
                    // 池子中最少资源数
                    // 'minResources' => 2,
                    // 资源回收时间间隔，单位：秒
                    // 'gcInterval' => 60,
                    // 获取资源最大存活时间，单位：秒
                    // 'maxActiveTime' => 3600,
                    // 等待资源最大超时时间，单位：毫秒
                    // 'waitTimeout' => 3000,
                    // 心跳时间间隔，单位：秒
                    // 'heartbeatInterval' => null,
                    // 当获取资源时，是否检查状态
                    // 'checkStateWhenGetResource' => true,
                    // 每次获取资源最长使用时间，单位：秒；为 null 则不限制
                    // 'maxUsedTime' => null,
                    // 资源创建后最大空闲回收时间，单位：秒；为 null 则不限制
                    // 'maxIdleTime' => null,
                    // 当前请求上下文资源检查状态间隔，单位：支持小数的秒；为 null 则不限制
                    // 'requestResourceCheckInterval' => 30,
                    // 负载均衡-轮流
                    // 'resourceConfigMode' => ResourceConfigMode::TURN,
                    // 负载均衡-随机
                    // 'resourceConfigMode' => ResourceConfigMode::RANDOM,
                ],
            ],
            // 连接池资源配置
            'resource' => [
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => 'root',
                'database' => 'database',
                'prefix'   => '', // 表前缀
                // 'port'    => '3306',
                // 'timeout' => '建立连接超时时间',
                // 'charset' => '',
                // 使用 hook pdo 驱动（缺省默认）
                // 'dbClass' => 'PdoMysqlDriver',
                // 使用 hook mysqli 驱动
                // 'dbClass' => 'MysqliDriver',
                // 使用 Swoole MySQL 驱动
                // 'dbClass' => 'SwooleMysqlDriver',
                // 数据库连接后，执行初始化的 SQL
                // 'initSqls' => [
                //     'select 1',
                //     'select 2',
                // ],
            ],
            // uri 写法
            // 'resource'  =>  [
            //     'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
            //     'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
            // ],
            // 'resource'  =>  'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60;tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
        ]
    ],
];
```

## 单例配置

> 用于 php-fpm、Workerman 下

```php
<?php

return [
    'db'    => [
        'defaultPool'   => 'alias1', // 默认连接名
        'statement'     =>  [
            'cache' =>  true, // 是否开启 statement 缓存，默认开启
        ],
        'connections' => [
            'alias1' => [
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => 'root',
                'database' => 'database',
                'prefix'   => '', // 表前缀
                // 'port'    => '3306',
                // 'timeout' => '建立连接超时时间',
                // 'charset' => '',
                // 使用 hook pdo 驱动（缺省默认）
                // 'dbClass' => 'PdoMysqlDriver',
                // 使用 hook mysqli 驱动
                // 'dbClass' => 'MysqliDriver',
                // 使用 Swoole MySQL 驱动
                // 'dbClass' => 'SwooleMysqlDriver',
                // 数据库连接后，执行初始化的 SQL
                // 'initSqls' => [
                //     'select 1',
                //     'select 2',
                // ],
                // 当获取资源时，是否检查状态
                // 'checkStateWhenGetResource' => true,
                // 心跳时间间隔，单位：秒
                // 'heartbeatInterval' => null,
            ],
        ],
    ],
];
```
