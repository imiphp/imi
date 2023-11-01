<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Process;

use Imi\Pool\Annotation\PoolClean;
use Imi\Pool\PoolManager;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;

#[Process(name: 'PoolTest1')]
class PoolTest1Process extends BaseProcess
{
    #[PoolClean]
    public function run(\Swoole\Process $process): void
    {
        $result = [];
        foreach (PoolManager::getNames() as $poolName)
        {
            $pool = PoolManager::getInstance($poolName);
            $result[$poolName] = $pool->getCount();
        }
        echo json_encode($result), \PHP_EOL;
        $process->exit();
    }
}
