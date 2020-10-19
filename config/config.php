<?php

return [
    'beanScan' => [
        'Imi\Config',
        'Imi\Bean',
        'Imi\Aop',
        'Imi\Annotation',
        'Imi\Cache',
        'Imi\Server',
        'Imi\Log',
        'Imi\Pool',
        'Imi\Db',
        'Imi\Redis',
        'Imi\Listener',
        'Imi\Model',
        'Imi\Task',
        'Imi\Tool',
        'Imi\Process',
        'Imi\HotUpdate',
        'Imi\Validate',
        'Imi\HttpValidate',
        'Imi\Enum',
        'Imi\Lock',
        'Imi\Facade',
        'Imi\Cron',
        'Imi\Util\Co',
    ],
    'atomics' => [
        'session',
        'imi.ConnectContextRedisLock',
        'imi.GroupRedisLock',
    ],
    // 跳过初始化的工具
    'skipInitTools' => [
        ['imi', 'buildImiRuntime'],
        ['imi', 'clearImiRuntime'],
    ],
];
