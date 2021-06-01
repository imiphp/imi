<?php

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Snowflake\Test\Test',
    ],
    'components'    => [
        // 引入本组件
        'snowflake'    => 'Imi\Snowflake',
    ],
    'ignoreNamespace'   => [
    ],
    // 连接池配置
    'pools'    => [
        'redis_test'    => [
            'sync'    => [
                'pool'    => [
                    'class'        => \Imi\Redis\SyncRedisPool::class,
                    'config'       => [
                        'maxResources'    => 128,
                        'minResources'    => 1,
                    ],
                ],
                'resource'    => [
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
                ],
            ],
            'async'    => [
                'pool'    => [
                    'class'        => \Imi\Swoole\Redis\Pool\CoroutineRedisPool::class,
                    'config'       => [
                        'maxResources'    => 128,
                        'minResources'    => 1,
                    ],
                ],
                'resource'    => [
                    'host'      => imiGetEnv('REDIS_SERVER_HOST', '127.0.0.1'),
                    'port'      => imiGetEnv('REDIS_SERVER_PORT', 6379),
                    'password'  => imiGetEnv('REDIS_SERVER_PASSWORD'),
                ],
            ],
        ],
    ],
    // redis 配置
    'redis' => [
        // 数默认连接池名
        'defaultPool'   => 'redis_test',
    ],
    'beans'    => [
        'JWT'   => [
            'list'  => [
                'a' => [
                    'audience'  => 'audience_a',
                    'subject'   => 'subject_a',
                    'expires'   => 86400,
                    'issuer'    => 'issuer_a',
                    'headers'   => [
                        'a' => '1',
                        'b' => '2',
                    ],
                    'tokenHandler'  => function () {
                        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImEiOiIxIiwiYiI6IjIifQ.eyJhdWQiOiJhdWRpZW5jZV9hIiwic3ViIjoic3ViamVjdF9hIiwiZXhwIjoxODkzMjkxNTE1LCJpc3MiOiJpc3N1ZXJfYSIsIm5iZiI6MCwianRpIjoiIiwiaWF0IjoxNTc3OTMxNTE1LCJkYXRhIjp7Im1lbWJlcklkIjoxOTI2MDgxN319.-tXlyj1BcVD8GJIE2nQdTPULVpZFD0h5BIQdx_X943E';
                    },
                    'privateKey'    => '123456',
                    'publicKey'     => '123456',
                ],
            ],
        ],
        'Snowflake' => [
            'list'  => [
                'testBasic' => [
                ],
                'test1' => [
                    'datacenterId'   => -1,
                    'workerId'       => -1,
                ],
                'test2' => [
                    'datacenterId'   => 33,
                    'workerId'       => -1,
                ],
                'test3' => [
                    'datacenterId'   => 1,
                    'workerId'       => 2,
                ],
                'test4' => [
                    'datacenterId'   => 999,
                    'workerId'       => 20,
                ],
                'testBatch' => [
                    'datacenterId'   => 999,
                    'workerId'       => 20,
                ],
                'testParseId' => [
                    'datacenterId'   => 999,
                    'workerId'       => 20,
                ],
            ],
        ],
    ],
];
