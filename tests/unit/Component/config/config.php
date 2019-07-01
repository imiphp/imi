<?php
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'Imi\Test\Component\Tests',
        'Imi\Test\Component\Aop',
        'Imi\Test\Component\Inject',
        'Imi\Test\Component\Event',
        'Imi\Test\Component\Enum',
        'Imi\Test\Component\Redis',
    ],
    'beans'    =>    [
    ],
    'imi'   =>  'very six',
    'yurun' =>  '',
    
    // 连接池配置
    'pools'    =>    [
        'redis_test'    =>    [
            'sync'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Redis\SyncRedisPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'        => '127.0.0.1',
                    'port'        =>    6379,
                ]
            ],
            'async'    =>    [
                'pool'    =>    [
                    'class'        =>    \Imi\Redis\CoroutineRedisPool::class,
                    'config'    =>    [
                        'maxResources'    =>    10,
                        'minResources'    =>    1,
                    ],
                ],
                'resource'    =>    [
                    'host'        => '127.0.0.1',
                    'port'        =>    6379,
                ]
            ],
        ],
    ],
    // redis 配置
    'redis' =>  [
        // 数默认连接池名
        'defaultPool'   =>  'redis_test',
    ],
];