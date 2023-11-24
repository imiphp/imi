<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\Pool;

use Imi\ConnectionCenter\Contract\IConnection;
use Imi\Swoole\Util\Co\ChannelContainer;

/**
 * 连接资源.
 */
class InstanceResource
{
    /**
     * @var \WeakReference<IConnection>|null
     */
    protected ?\WeakReference $connection = null;

    /**
     * 是否空闲状态
     */
    protected bool $isFree = true;

    /**
     * 创建时间的时间戳.
     */
    protected float $createTime = 0;

    /**
     * 最后一次使用的时间戳.
     */
    protected float $lastUseTime = 0;

    /**
     * 最后一次被释放的时间戳.
     */
    protected float $lastReleaseTime = 0;

    public function __construct(protected object $instance)
    {
    }

    public function __destruct()
    {
        $id = (string) spl_object_id($this);
        if (ChannelContainer::hasChannel($id))
        {
            ChannelContainer::removeChannel($id);
        }
    }

    public function setConnection(IConnection $connection): self
    {
        $this->connection = \WeakReference::create($connection);

        return $this;
    }

    /**
     * @return \WeakReference<IConnection>
     */
    public function getConnection(): ?\WeakReference
    {
        return $this->connection;
    }

    public function hasConnection(): bool
    {
        return $this->connection && $this->connection->get();
    }

    public function lock(float $timeout = 0): bool
    {
        if ($this->isFree || ChannelContainer::pop((string) spl_object_id($this), $timeout))
        {
            $this->isFree = false;
            $this->lastUseTime = microtime(true);

            return true;
        }

        return false;
    }

    public function release(): void
    {
        $this->isFree = true;
        $this->lastReleaseTime = microtime(true);
        $id = (string) spl_object_id($this);
        if (ChannelContainer::hasChannel($id))
        {
            $channel = ChannelContainer::getChannel($id);
            if (($channel->stats()['consumer_num'] ?? 0) > 0)
            {
                $channel->push(true);
            }
        }
    }

    /**
     * 是否空闲状态
     */
    public function isFree(): bool
    {
        return $this->isFree;
    }

    public function getInstance(): object
    {
        return $this->instance;
    }

    public function setInstance(object $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get 创建时间的时间戳.
     */
    public function getCreateTime(): float
    {
        return $this->createTime;
    }

    /**
     * Get 最后一次使用的时间戳.
     */
    public function getLastUseTime(): float
    {
        return $this->lastUseTime;
    }

    /**
     * Get 最后一次被释放的时间戳.
     */
    public function getLastReleaseTime(): float
    {
        return $this->lastReleaseTime;
    }
}
