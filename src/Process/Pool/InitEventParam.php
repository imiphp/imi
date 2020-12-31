<?php

declare(strict_types=1);

namespace Imi\Process\Pool;

use Imi\Process\Pool;
use Imi\Event\EventParam;

class InitEventParam extends EventParam
{
    /**
     * 进程池对象
     *
     * @var \Imi\Process\Pool
     */
    protected Pool $pool;

    /**
     * Get 进程池对象
     *
     * @return \Imi\Process\Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }
}
