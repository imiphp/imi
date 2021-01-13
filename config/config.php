<?php

declare(strict_types=1);

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
        'Imi\Model',
        'Imi\Swoole\Task',
        'Imi\Tool',
        'Imi\Cli',
        'Imi\Validate',
        'Imi\HttpValidate',
        'Imi\Enum',
        'Imi\Lock',
        'Imi\Facade',
        'Imi\Cron',
        'Imi\Util\Co',
        'Imi\RequestContextProxy',
    ],
    'ignoreNamespace'   => [
        'Imi\functions',
        'Imi\Cli\bootstrap',
        'Imi\Components\*',
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
