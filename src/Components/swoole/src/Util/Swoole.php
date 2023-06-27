<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

class Swoole
{
    private function __construct()
    {
    }

    /**
     * 获取master进程pid.
     */
    public static function getMasterPID(): int
    {
        return ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer()->master_pid;
    }

    /**
     * 获取manager进程pid.
     */
    public static function getManagerPID(): int
    {
        return ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer()->manager_pid;
    }
}
