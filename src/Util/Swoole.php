<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\ServerManage;

class Swoole
{
    private function __construct()
    {
    }

    /**
     * 获取master进程pid.
     *
     * @return int
     */
    public static function getMasterPID(): int
    {
        return ServerManage::getServer('main')->getSwooleServer()->master_pid;
    }

    /**
     * 获取manager进程pid.
     *
     * @return int
     */
    public static function getManagerPID(): int
    {
        return ServerManage::getServer('main')->getSwooleServer()->manager_pid;
    }
}
