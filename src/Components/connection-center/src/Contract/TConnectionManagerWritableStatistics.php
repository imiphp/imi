<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

trait TConnectionManagerWritableStatistics
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

    public function changeTotalConnectionCount(int $quantity): int
    {
        return $this->totalConnectionCount += $quantity;
    }

    /**
     * @codeCoverageIgnore
     */
    public function changeFreeConnectionCount(int $quantity): int
    {
        return $this->freeConnectionCount += $quantity;
    }

    public function changeUsedConnectionCount(int $quantity): int
    {
        return $this->usedConnectionCount += $quantity;
    }
}
