<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\Singleton;

use Imi\ConnectionCenter\Contract\TConnectionManagerWritableStatistics;

/**
 * 单例连接管理器可写状态.
 */
class SingletonConnectionManagerWritableStatistics extends SingletonConnectionManagerStatistics
{
    use TConnectionManagerWritableStatistics;

    public function toStatus(): SingletonConnectionManagerStatistics
    {
        return new SingletonConnectionManagerStatistics(
            $this->createConnectionTimes,
            $this->getConnectionTimes,
            $this->releaseConnectionTimes,
            $this->totalConnectionCount,
            $this->freeConnectionCount,
            $this->usedConnectionCount,
            $this->maxGetConnectionTime,
            $this->minGetConnectionTime,
            $this->lastGetConnectionTime
        );
    }
}
