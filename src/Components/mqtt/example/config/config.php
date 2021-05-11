<?php

return [
    // 项目根命名空间
    'namespace'    => 'MQTTApp',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'MQTTApp\Listener',
        'MQTTApp\Task',
    ],

    // 组件命名空间
    'components'    => [
        'MQTT'  => 'Imi\MQTT',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'     => 'MQTTApp\MQTTServer',
        'type'          => 'MQTT',
        'host'          => '127.0.0.1',
        'port'          => 8081,
        'configs'       => [
            'worker_num'        => 1,
        ],
        'controller'    => \MQTTApp\MQTTServer\Controller\MQTTController::class,
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
        'MQTTSSL'   => [
            'namespace'     => 'MQTTApp\MQTTSServer',
            'type'          => 'MQTT',
            'host'          => '127.0.0.1',
            'port'          => 8082,
            'sockType'      => \SWOOLE_SOCK_TCP | \SWOOLE_SSL, // SSL 需要设置一下 sockType
            'configs'       => [
                // 配置证书
                'ssl_cert_file'     => dirname(__DIR__) . '/ssl/server.crt',
                'ssl_key_file'      => dirname(__DIR__) . '/ssl/server.key',
            ],
            'controller'    => \MQTTApp\MQTTServer\Controller\MQTTController::class,
        ],
    ],

    // 连接池配置
    'pools'    => [
        'redis'    => [
            'sync'    => [
                'pool'    => [
                    'class'        => \Imi\Redis\SyncRedisPool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 0,
                    ],
                ],
                'resource'    => [
                    'host'      => '127.0.0.1',
                    'port'      => 6379,
                    'password'  => null,
                ],
            ],
            'async'    => [
                'pool'    => [
                    'class'        => \Imi\Redis\CoroutineRedisPool::class,
                    'config'       => [
                        'maxResources'    => 10,
                        'minResources'    => 1,
                    ],
                ],
                'resource'    => [
                    'host'      => '127.0.0.1',
                    'port'      => 6379,
                    'password'  => null,
                ],
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
            'redisConnectContextLock' => [
                'class'     => 'RedisLock',
                'options'   => [
                    'poolName'  => 'redis',
                ],
            ],
        ],
    ],
];
