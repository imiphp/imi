<?php

namespace Imi\Lock\Handler;

interface ILockHandler
{
    public function __construct($id, $options = []);

    /**
     * 加锁，会挂起协程.
     *
     * @param callable $taskCallable      加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     * @param callable $afterLockCallable 当获得锁后执行的回调，只有当 $taskCallable 不为 null 时有效。该回调返回 true 则不执行 $taskCallable
     *
     * @return bool
     */
    public function lock($taskCallable = null, $afterLockCallable = null): bool;

    /**
     * 尝试获取锁
     *
     * @param callable $taskCallable 加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     *
     * @return bool
     */
    public function tryLock($taskCallable = null): bool;

    /**
     * 解锁
     *
     * @return bool
     */
    public function unlock(): bool;

    /**
     * 获取当前是否已获得锁状态
     *
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * 获取锁的唯一ID.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * 解锁并释放所有资源.
     *
     * @return void
     */
    public function close();

    /**
     * Get 等待锁超时时间，单位：毫秒，0为不限制.
     *
     * @return int
     */
    public function getWaitTimeout(): int;

    /**
     * Get 锁超时时间，单位：毫秒.
     *
     * @return int
     */
    public function getLockExpire(): int;

    /**
     * 获取获得锁的协程ID.
     *
     * @return int
     */
    public function getLockCoId(): int;
}
