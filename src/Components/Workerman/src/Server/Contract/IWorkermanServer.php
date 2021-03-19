<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Contract;

use Imi\Server\Contract\IServer;
use Imi\Server\Group\Contract\IServerGroup;
use Workerman\Worker;

interface IWorkermanServer extends IServer, IServerGroup
{
    /**
     * 获取 Workerman Worker 对象
     */
    public function getWorker(): Worker;
}
