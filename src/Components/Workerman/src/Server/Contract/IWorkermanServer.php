<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Contract;

use Imi\Server\Contract\IServer;
use Workerman\Worker;

interface IWorkermanServer extends IServer
{
    /**
     * 获取 Workerman Worker 对象
     */
    public function getWorker(): Worker;
}
