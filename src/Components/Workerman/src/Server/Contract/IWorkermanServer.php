<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Contract;

use Imi\Server\Contract\IServer;
use Workerman\Worker;

interface IWorkermanServer extends IServer
{
    /**
     * 获取 Workerman Worker 对象
     *
     * @return \Workerman\Worker
     */
    public function getWorker(): Worker;
}
