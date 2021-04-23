<?php

namespace Imi\Lock\Handler;

use Imi\Util\Coroutine;

abstract class BaseLock implements ILockHandler
{
    /**
     * 锁的唯一 ID.
     *
     * @var string
     */
    protected $id;

    /**
     * 是否已加锁
     *
     * @var bool
     */
    protected $isLocked = false;

    /**
     * 等待锁超时时间，单位：毫秒，0为不限制.
     *
     * @var int
     */
    protected $waitTimeout = 3000;

    /**
     * 锁超时时间，单位：毫秒.
     *
     * @var int
     */
    protected $lockExpire = 3000;

    /**
     * 获得锁的协程ID.
     *
     * @var int
     */
    private $lockCoId = -1;

    /**
     * @param string $id
     * @param array  $options
     */
    public function __construct($id, $options = [])
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
     * 获取锁的唯一ID.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * 加锁，会挂起协程.
     *
     * @param callable $taskCallable      加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     * @param callable $afterLockCallable 当获得锁后执行的回调，只有当 $taskCallable 不为 null 时有效。该回调返回 true 则不执行 $taskCallable
     *
     * @return bool
     */
    public function lock($taskCallable = null, $afterLockCallable = null): bool
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
        $this->lockCoId = Coroutine::getuid();
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
     * 尝试获取锁
     *
     * @param callable $taskCallable 加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     *
     * @return bool
     */
    public function tryLock($taskCallable = null): bool
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
        $this->lockCoId = Coroutine::getuid();
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
     * 解锁
     *
     * @return bool
     */
    public function unlock(): bool
    {
        if (!$this->isLocked)
        {
            return false;
        }
        if (!$this->__unlock())
        {
            return false;
        }
        $this->isLocked = false;
        $this->lockCoId = -1;

        return true;
    }

    /**
     * 获取当前是否已获得锁状态
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked && $this->lockCoId === Coroutine::getuid();
    }

    /**
     * 解锁并释放所有资源.
     *
     * @return void
     */
    public function close()
    {
        if ($this->isLocked)
        {
            $this->unlock();
        }
        $this->__close();
    }

    /**
     * 解锁并释放所有资源.
     *
     * @return void
     */
    protected function __close()
    {
    }

    /**
     * 加锁，会挂起协程.
     *
     * @return bool
     */
    abstract protected function __lock(): bool;

    /**
     * 尝试获取锁
     *
     * @return bool
     */
    abstract protected function __tryLock(): bool;

    /**
     * 解锁
     *
     * @return bool
     */
    abstract protected function __unlock(): bool;

    /**
     * Get 等待锁超时时间，单位：毫秒，0为不限制.
     *
     * @return int
     */
    public function getWaitTimeout(): int
    {
        return $this->waitTimeout;
    }

    /**
     * Get 锁超时时间，单位：毫秒.
     *
     * @return int
     */
    public function getLockExpire(): int
    {
        return $this->lockExpire;
    }

    /**
     * 获取获得锁的协程ID.
     *
     * @return int
     */
    public function getLockCoId(): int
    {
        return $this->lockCoId;
    }
}
