<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Process;

use Imi\Pool\Annotation\PoolClean;
use Imi\Pool\PoolManager;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;

/**
 * @Process("PoolTest2")
 */
class PoolTest2Process extends BaseProcess
{
    /**
     * @PoolClean(mode="deny", list={"maindb"})
     */
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
