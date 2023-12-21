<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

abstract class AbstractConnectionManagerStatistics implements IConnectionManagerStatistics
{
    public function __construct(protected int $createConnectionTimes = 0, protected int $getConnectionTimes = 0, protected int $releaseConnectionTimes = 0, protected int $totalConnectionCount = 0, protected int $freeConnectionCount = 0, protected int $usedConnectionCount = 0, protected float $maxGetConnectionTime = 0, protected float $minGetConnectionTime = \PHP_FLOAT_MAX, protected float $lastGetConnectionTime = 0)
    {
    }

    public function getCreateConnectionTimes(): int
    {
        return $this->createConnectionTimes;
    }

    public function getGetConnectionTimes(): int
    {
        return $this->getConnectionTimes;
    }

    public function getReleaseConnectionTimes(): int
    {
        return $this->releaseConnectionTimes;
    }

    public function getTotalConnectionCount(): int
    {
        return $this->totalConnectionCount;
    }

    public function getFreeConnectionCount(): int
    {
        return $this->freeConnectionCount;
    }

    public function getUsedConnectionCount(): int
    {
        return $this->usedConnectionCount;
    }

    public function getMaxGetConnectionTime(): float
    {
        return $this->maxGetConnectionTime;
    }

    public function getMinGetConnectionTime(): float
    {
        return $this->minGetConnectionTime;
    }

    public function getLastGetConnectionTime(): float
    {
        return $this->lastGetConnectionTime;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'createConnectionTimes'  => $this->createConnectionTimes,
            'getConnectionTimes'     => $this->getConnectionTimes,
            'releaseConnectionTimes' => $this->releaseConnectionTimes,
            'totalConnectionCount'   => $this->totalConnectionCount,
            'freeConnectionCount'    => $this->freeConnectionCount,
            'usedConnectionCount'    => $this->usedConnectionCount,
            'maxGetConnectionTime'   => $this->maxGetConnectionTime,
            'minGetConnectionTime'   => $this->minGetConnectionTime,
            'lastGetConnectionTime'  => $this->lastGetConnectionTime,
        ];
    }
}
