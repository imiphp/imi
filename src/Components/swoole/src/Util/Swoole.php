<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

class Swoole
{
    use \Imi\Util\Traits\TStaticClass;

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

    public static function getTcpSockTypeByHost(string $host): int
    {
        return filter_var($host, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6) ? \SWOOLE_SOCK_TCP6 : \SWOOLE_SOCK_TCP;
    }

    public static function getUdpSockTypeByHost(string $host): int
    {
        return filter_var($host, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6) ? \SWOOLE_SOCK_UDP6 : \SWOOLE_SOCK_UDP;
    }
}
