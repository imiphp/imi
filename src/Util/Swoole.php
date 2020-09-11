<?php

namespace Imi\Util;

use Imi\ServerManage;

abstract class Swoole
{
    /**
     * 获取master进程pid.
     *
     * @return int
     */
    public static function getMasterPID()
    {
        return ServerManage::getServer('main')->getSwooleServer()->master_pid;
    }

    /**
     * 获取manager进程pid.
     *
     * @return int
     */
    public static function getManagerPID()
    {
        return ServerManage::getServer('main')->getSwooleServer()->manager_pid;
    }
}
