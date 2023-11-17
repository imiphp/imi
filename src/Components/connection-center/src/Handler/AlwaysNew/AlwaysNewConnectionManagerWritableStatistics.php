<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\AlwaysNew;

/**
 * 总是创建新连接管理器可写状态.
 */
class AlwaysNewConnectionManagerWritableStatistics extends AlwaysNewConnectionManagerStatistics
{
    public function addCreateConnectionTimes(): int
    {
        return ++$this->createConnectionTimes;
    }

    public function addGetConnectionTimes(): int
    {
        return ++$this->getConnectionTimes;
    }

    public function addReleaseConnectionTimes(): int
    {
        return ++$this->releaseConnectionTimes;
    }

    public function setGetConnectionTime(float $time): void
    {
        $this->lastGetConnectionTime = $time;
        if ($time < $this->minGetConnectionTime)
        {
            $this->minGetConnectionTime = $time;
        }
        if ($time > $this->maxGetConnectionTime)
        {
            $this->maxGetConnectionTime = $time;
        }
    }

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
