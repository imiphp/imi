<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\Pool;

use Imi\ConnectionCenter\Contract\TConnectionManagerWritableStatistics;

/**
 * 连接池连接管理器可写状态.
 */
class PoolConnectionManagerWritableStatistics extends PoolConnectionManagerStatistics
{
    use TConnectionManagerWritableStatistics;

    public function toStatus(int $totalConnectionCount, int $freeConnectionCount): PoolConnectionManagerStatistics
    {
        return new PoolConnectionManagerStatistics(
            $this->createConnectionTimes,
            $this->getConnectionTimes,
            $this->releaseConnectionTimes,
            $totalConnectionCount,
            $freeConnectionCount,
            $totalConnectionCount - $freeConnectionCount,
            $this->maxGetConnectionTime,
            $this->minGetConnectionTime,
            $this->lastGetConnectionTime
        );
    }
}
