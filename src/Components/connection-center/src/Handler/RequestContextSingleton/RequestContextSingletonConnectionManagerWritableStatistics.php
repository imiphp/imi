<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\RequestContextSingleton;

use Imi\ConnectionCenter\Contract\TConnectionManagerWritableStatistics;

/**
 * 请求上下文单例连接管理器可写状态.
 */
class RequestContextSingletonConnectionManagerWritableStatistics extends RequestContextSingletonConnectionManagerStatistics
{
    use TConnectionManagerWritableStatistics;

    public function toStatus(): RequestContextSingletonConnectionManagerStatistics
    {
        return new RequestContextSingletonConnectionManagerStatistics(
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
