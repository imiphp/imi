<?php

namespace Imi\Phar;

class Constant
{
    public const CONTAINER_SET = [
        'swoole',
        'workerman',
        'roadrunner',
    ];

    public const CONTAINER_BOOTSTRAP = [
        'swoole'     => 'vendor/imiphp/imi-swoole/bootstrap.php',
        'workerman'  => 'vendor/imiphp/imi-workerman/bootstrap.php',
        'roadrunner' => 'vendor/imiphp/imi-roadrunner/bootstrap.php',
    ];

    public const CFG_FILE_NAME = '.imi-phar-cfg.php';
}
