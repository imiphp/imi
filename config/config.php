<?php

declare(strict_types=1);

$imiPath = \dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'src' . \DIRECTORY_SEPARATOR;

return [
    'beanScan' => [
        'Imi\Config',
        'Imi\Bean',
        'Imi\Aop',
        'Imi\Async',
        'Imi\Annotation',
        'Imi\Cache',
        'Imi\Server',
        'Imi\Log',
        'Imi\Pool',
        'Imi\Db',
        'Imi\Redis',
        'Imi\Model',
        'Imi\Tool',
        'Imi\Cli',
        'Imi\Validate',
        'Imi\HttpValidate',
        'Imi\Enum',
        'Imi\Lock',
        'Imi\Facade',
        'Imi\Cron',
        'Imi\RequestContextProxy',
        'Imi\Process',
    ],
    'ignoreNamespace'   => [
        'Imi\Components\*',
    ],
    'ignorePaths'   => [
        $imiPath . 'functions',
        $imiPath . 'Components' . \DIRECTORY_SEPARATOR . '*' . \DIRECTORY_SEPARATOR . 'vendor',
        $imiPath . 'Cli' . \DIRECTORY_SEPARATOR . 'bootstrap',
    ],
];
