<?php

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'MQTTApp\MQTTServer\Controller',
    ],
    'beans'    => [
        'ConnectContextStore'   => [
            'handlerClass'  => 'ConnectContextRedis',
        ],
        'ConnectContextRedis'    => [
            'redisPool' => 'redis',
            'lockId'    => 'redisConnectContextLock',
        ],
    ],
];
