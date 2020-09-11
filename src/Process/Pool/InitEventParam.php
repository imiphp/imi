<?php

namespace Imi\Process\Pool;

use Imi\Event\EventParam;

class InitEventParam extends EventParam
{
    /**
     * 进程池对象
     *
     * @var \Imi\Process\Pool
     */
    protected $pool;

    /**
     * Get 进程池对象
     *
     * @return \Imi\Process\Pool
     */
    public function getPool()
    {
        return $this->pool;
    }
}
