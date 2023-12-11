<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\CommonEvent;
use Imi\Swoole\Process\Pool;

class InitEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 进程池对象
         */
        public readonly ?Pool $pool = null
    ) {
        parent::__construct('inited', $pool);
    }

    /**
     * Get 进程池对象
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }
}
