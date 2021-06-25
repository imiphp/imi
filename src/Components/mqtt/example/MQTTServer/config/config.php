<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'MQTTApp\MQTTServer\Controller',
    ],
    'beans'    => [
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextRedis',
        ],
        'ConnectionContextRedis'    => [
            'redisPool' => 'redis',
            'lockId'    => 'redisConnectionContextLock',
        ],
    ],
];
