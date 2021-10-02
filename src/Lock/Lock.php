<?php

declare(strict_types=1);

namespace Imi\Lock;

use Imi\App;
use Imi\Config;
use Imi\Lock\Handler\ILockHandler;

class Lock
{
    /**
     * 配置.
     *
     * @var \Imi\Lock\LockConfigOption[]
     */
    private static array $options = [];

    /**
     * 对象列表.
     *
     * @var \Imi\Lock\Handler\ILockHandler[]
     */
    private static array $instances = [];

    private static bool $inited = false;

    private function __construct()
    {
    }

    public static function init(): void
    {
        self::$options = self::$instances = [];
        foreach (Config::getAliases() as $alias)
        {
            foreach (Config::get($alias . '.lock.list', []) as $id => $option)
            {
                self::add($id, $option);
            }
        }
        self::$inited = true;
    }

    /**
     * @return LockConfigOption[]
     */
    public static function getOptions(): array
    {
        return self::$options;
    }

    /**
     * 获取锁对象
     */
    public static function getInstance(?string $lockConfigId = null, ?string $lockId = null): ILockHandler
    {
        if (!self::$inited)
        {
            self::init();
        }
        if (!$lockConfigId)
        {
            $lockConfigId = static::getDefaultId();
        }
        $instances = &static::$instances;
        if (null === $lockId && isset($instances[$lockConfigId]))
        {
            return $instances[$lockConfigId];
        }
        $options = &static::$options;
        if (!isset($options[$lockConfigId]))
        {
            throw new \RuntimeException(sprintf('Lock %s does not exists', $lockConfigId));
        }
        $option = $options[$lockConfigId];
        if (null === $lockId)
        {
            return $instances[$lockConfigId] = App::getBean($option->class, $lockConfigId, $option->options);
        }
        else
        {
            return App::getBean($option->class, $lockId, $option->options);
        }
    }

    /**
     * 获取默认锁ID.
     */
    public static function getDefaultId(): string
    {
        return Config::get('@currentServer.lock.default');
    }

    /**
     * 增加配置.
     */
    public static function add(string $id, array $option): void
    {
        static::$options[$id] = new LockConfigOption($option);
    }

    /**
     * 加锁，会挂起协程.
     *
     * @param callable|null $taskCallable      加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     * @param callable|null $afterLockCallable 当获得锁后执行的回调，只有当 $taskCallable 不为 null 时有效。该回调返回 true 则不执行 $taskCallable
     */
    public static function lock(?string $id = null, ?callable $taskCallable = null, ?callable $afterLockCallable = null): bool
    {
        return static::getInstance($id)->lock($taskCallable, $afterLockCallable);
    }

    /**
     * 尝试获取锁
     *
     * @param callable|null $taskCallable 加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     */
    public static function tryLock(?string $id = null, ?callable $taskCallable = null): bool
    {
        return static::getInstance($id)->tryLock($taskCallable);
    }

    /**
     * 解锁
     */
    public static function unlock(?string $id = null): bool
    {
        return static::getInstance($id)->unlock();
    }

    /**
     * 获取当前是否已获得锁状态
     */
    public static function isLocked(?string $id = null): bool
    {
        return static::getInstance($id)->isLocked();
    }
}
