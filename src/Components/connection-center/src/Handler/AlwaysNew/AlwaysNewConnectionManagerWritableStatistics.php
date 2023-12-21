<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\AlwaysNew;

use Imi\ConnectionCenter\Contract\TConnectionManagerWritableStatistics;

/**
 * 总是创建新连接管理器可写状态.
 */
class AlwaysNewConnectionManagerWritableStatistics extends AlwaysNewConnectionManagerStatistics
{
    use TConnectionManagerWritableStatistics;

    public function toStatus(): AlwaysNewConnectionManagerStatistics
    {
        return new AlwaysNewConnectionManagerStatistics(
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
