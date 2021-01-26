<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Workerman\Worker;

class WorkermanServerWorker extends Worker
{
    public static function getMasterPid(): int
    {
        return static::$_masterPid;
    }
}
