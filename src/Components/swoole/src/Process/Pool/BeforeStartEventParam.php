<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\CommonEvent;
use Imi\Swoole\Process\Pool;

class BeforeStartEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 进程池对象
         */
        public readonly ?Pool $pool = null
    ) {
        parent::__construct('before.start', $pool);
    }

    /**
     * Get 进程池对象
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }
}
