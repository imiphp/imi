<?php

declare(strict_types=1);

namespace Imi\Phar;

class Constant
{
    public const CONTAINER_SET = [
        'swoole',
        'workerman',
        'roadrunner',
        'cli',
    ];

    public const CONTAINER_BOOTSTRAP = [
        'swoole'     => 'vendor/imiphp/imi-swoole/bootstrap.php',
        'workerman'  => 'vendor/imiphp/imi-workerman/bootstrap.php',
        'roadrunner' => 'vendor/imiphp/imi-roadrunner/bootstrap.php',
        'cli'        => 'vendor/imiphp/imi/src/Cli/bootstrap.php',
    ];

    public const CONTAINER_PACKAGE = [
        'swoole'     => 'imiphp/imi-swoole',
        'workerman'  => 'imiphp/imi-workerman',
        'roadrunner' => 'imiphp/imi-roadrunner',
        'cli'        => 'imiphp/imi',
    ];

    public const CFG_FILE_NAME = '.imi-phar-cfg.php';
}
