<?php

namespace Imi\Test\HttpServer\Process;

use Imi\Pool\Annotation\PoolClean;
use Imi\Pool\PoolManager;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;

/**
 * @Process("PoolTest2")
 */
class PoolTest2Process extends BaseProcess
{
    /**
     * @PoolClean(mode="deny", list={"maindb"})
     *
     * @param \Swoole\Process $process
     *
     * @return void
     */
    public function run(\Swoole\Process $process)
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
