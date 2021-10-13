<?php

declare(strict_types=1);

namespace Imi\Lock\Handler;

use Imi\Lock\Exception\LockFailException;
use Imi\Log\Log;
use Imi\RequestContext;
use function microtime;
use function sprintf;

abstract class BaseLock implements ILockHandler
{
    /**
     * 锁的唯一 ID.
     */
    protected string $id = '';

    /**
     * 是否已加锁
     */
    protected bool $isLocked = false;

    /**
     * 等待锁超时时间，单位：毫秒，0为不限制.
     */
    protected int $waitTimeout = 3000;

    /**
     * 锁超时时间，单位：毫秒.
     */
    protected int $lockExpire = 3000;

    /**
     * 获得锁的协程ID.
     */
    private string $lockCoId = '';

    /**
     * 执行时间.
     */
    protected float $beginTime = 0;

    /**
     * 执行超时抛出异常.
     */
    protected bool $timeoutException = false;

    /**
     * 解锁失败抛出异常.
     */
    protected bool $unlockException = false;

    public function __construct(string $id, array $options = [])
    {
        $this->id = $id;
        if ($options)
        {
            foreach ($options as $k => $v)
            {
                $this->$k = $v;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function lock(?callable $taskCallable = null, ?callable $afterLockCallable = null): bool
    {
        if ($this->isLocked())
        {
            return false;
        }
        if (!$this->__lock())
        {
            return false;
        }
        $this->isLocked = true;
        $this->lockCoId = RequestContext::getCurrentFlag();
        $this->beginTime = microtime(true);
        if (null === $taskCallable)
        {
            return true;
        }
        else
        {
            try
            {
                if (null !== $afterLockCallable && true === $afterLockCallable())
                {
                    return true;
                }
                $taskCallable();

                return true;
            }
            finally
            {
                $this->unlock();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function tryLock(?callable $taskCallable = null): bool
    {
        if ($this->isLocked())
        {
            return false;
        }
        if (!$this->__tryLock())
        {
            return false;
        }
        $this->isLocked = true;
        $this->lockCoId = RequestContext::getCurrentFlag();
        $this->beginTime = microtime(true);
        if (null !== $taskCallable)
        {
            try
            {
                $taskCallable();
            }
            finally
            {
                $this->unlock();
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function unlock(): bool
    {
        if (!$this->isLocked)
        {
            return false;
        }
        $executeTime = microtime(true) - $this->beginTime;
        if ($executeTime * 1000 > $this->lockExpire)
        {
            $message = sprintf('Lock execute timeout, id:%s, set timeout for %.3fs, execute time for %.3fs', $this->id, $this->lockExpire / 1000, $executeTime);
            if ($this->timeoutException)
            {
                throw new LockFailException($message);
            }
            else
            {
                Log::warning($message);
            }
        }
        if (!$this->__unlock())
        {
            $message = sprintf('Unlock failed, id:%s', $this->id);
            if ($this->unlockException)
            {
                throw new LockFailException($message);
            }
            else
            {
                Log::warning($message);
            }

            return false;
        }
        $this->isLocked = false;
        $this->lockCoId = '';

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isLocked(): bool
    {
        return $this->isLocked && $this->lockCoId === RequestContext::getCurrentFlag();
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        if ($this->isLocked)
        {
            $this->unlock();
        }
        $this->__close();
    }

    /**
     * 解锁并释放所有资源.
     */
    protected function __close(): void
    {
    }

    /**
     * 加锁，会挂起协程.
     */
    abstract protected function __lock(): bool;

    /**
     * 尝试获取锁
     */
    abstract protected function __tryLock(): bool;

    /**
     * 解锁
     */
    abstract protected function __unlock(): bool;

    /**
     * {@inheritDoc}
     */
    public function getWaitTimeout(): int
    {
        return $this->waitTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function getLockExpire(): int
    {
        return $this->lockExpire;
    }

    /**
     * {@inheritDoc}
     */
    public function getLockFlag(): string
    {
        return $this->lockCoId;
    }
}
