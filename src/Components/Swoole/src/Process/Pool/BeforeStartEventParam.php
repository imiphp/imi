<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\EventParam;
use Imi\Swoole\Process\Pool;

class BeforeStartEventParam extends EventParam
{
    /**
     * 进程池对象
     */
    protected Pool $pool;

    /**
     * Get 进程池对象
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }
}
