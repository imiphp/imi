<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter;

use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Contract\IConnectionManager;
use Imi\ConnectionCenter\Enum\ConnectionStatus;

class Connection implements IConnection
{
    protected ConnectionStatus $status = ConnectionStatus::Available;

    public function __construct(protected IConnectionManager $manager, protected mixed $instance)
    {
    }

    public function __destruct()
    {
        if (ConnectionStatus::Available === $this->status)
        {
            $this->release();
        }
    }

    public function getManager(): IConnectionManager
    {
        return $this->manager;
    }

    public function getInstance(): mixed
    {
        if (ConnectionStatus::Unavailable === $this->status)
        {
            throw new \RuntimeException('Connection is not available');
        }

        return $this->instance;
    }

    public function release(): void
    {
        if (ConnectionStatus::Available === $this->status)
        {
            $this->status = ConnectionStatus::WaitRelease;
            $this->manager->releaseConnection($this);
            $this->status = ConnectionStatus::Unavailable;
        }
        else
        {
            throw new \RuntimeException('Connection is not available');
        }
    }

    public function detach(): void
    {
        if (ConnectionStatus::Available === $this->status)
        {
            $this->manager->detachConnection($this);
        }
        else
        {
            throw new \RuntimeException('Connection is not available');
        }
    }

    public function getStatus(): ConnectionStatus
    {
        return $this->status;
    }
}
