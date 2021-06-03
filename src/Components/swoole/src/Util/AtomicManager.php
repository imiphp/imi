<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

/**
 * 原子计数管理类.
 */
class AtomicManager
{
    private function __construct()
    {
    }

    /**
     * 是否已初始化过.
     */
    private static bool $isInited = false;

    /**
     * \Swoole\Atomic 数组.
     */
    private static array $atomics = [];

    /**
     * 初始化.
     */
    public static function init(): void
    {
        if (static::$isInited)
        {
            throw new \RuntimeException('AtomicManager can not repeated init');
        }
        foreach (static::$atomics as $name => $value)
        {
            static::$atomics[$name] = new \Swoole\Atomic((int) $value);
        }
        static::$isInited = true;
    }

    /**
     * 增加原子计数对象名称.
     */
    public static function addName(string $name, int $initValue = 0): void
    {
        if (static::$isInited)
        {
            throw new \RuntimeException('AddName failed, AtomicManager was inited');
        }
        static::$atomics[$name] = $initValue;
    }

    /**
     * 设置原子计数对象名称.
     *
     * @param string[] $names
     */
    public static function setNames(array $names): void
    {
        if (static::$isInited)
        {
            throw new \RuntimeException('AddName failed, AtomicManager was inited');
        }
        foreach ($names as $key => $value)
        {
            if (is_numeric($key))
            {
                static::$atomics[$value] = 0;
            }
            else
            {
                static::$atomics[$key] = $value;
            }
        }
    }

    /**
     * 获取所有原子计数对象名称.
     */
    public static function getNames(): array
    {
        return array_keys(static::$atomics);
    }

    /**
     * 获取原子计数类实例.
     */
    public static function getInstance(string $name): \Swoole\Atomic
    {
        if (!static::$isInited)
        {
            throw new \RuntimeException('GetInstance failed, AtomicManager is not initialized');
        }
        if (!isset(static::$atomics[$name]))
        {
            throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
        }

        return static::$atomics[$name];
    }

    /**
     * 增加计数，返回结果数值
     *
     * @param string $name  原子计数对象名称
     * @param int    $value 要增加的数值，默认为1。与原值相加如果超过42亿，将会溢出，高位数值会被丢弃
     */
    public static function add(string $name, int $value = 1): int
    {
        return static::getInstance($name)->add($value);
    }

    /**
     * 减少计数，返回结果数值
     *
     * @param string $name  原子计数对象名称
     * @param int    $value 要减少的数值，默认为1。与原值相加如果低于0将会溢出，高位数值会被丢弃
     */
    public static function sub(string $name, int $value = 1): int
    {
        return static::getInstance($name)->sub($value);
    }

    /**
     * 获取当前计数的值
     *
     * @param string $name 原子计数对象名称
     */
    public static function get(string $name): int
    {
        return static::getInstance($name)->get();
    }

    /**
     * 将当前值设置为指定的数字。
     *
     * @param string $name 原子计数对象名称
     */
    public static function set(string $name, int $value): void
    {
        static::getInstance($name)->set($value);
    }

    /**
     * 如果当前数值等于$cmpValue返回true，并将当前数值设置为$setValue
     * 如果不等于返回false.
     *
     * @param string $name 原子计数对象名称
     */
    public static function cmpset(string $name, int $cmpValue, int $setValue): bool
    {
        return static::getInstance($name)->cmpset($cmpValue, $setValue);
    }

    /**
     * 当原子计数的值为0时程序进入等待状态。另外一个进程调用wakeup可以再次唤醒程序。底层基于Linux Futex实现，使用此特性，可以仅用4字节内存实现一个等待、通知、锁的功能。
     * 超时返回false，错误码为EAGAIN，可使用swoole_errno函数获取
     * 成功返回true，表示有其他进程通过wakeup成功唤醒了当前的锁
     * 使用wait/wakeup特性时，原子计数的值只能为0或1，否则会导致无法正常使用
     * 当然原子计数的值为1时，表示不需要进入等待状态，资源当前就是可用。wait函数会立即返回true.
     *
     * @param string $name    原子计数对象名称
     * @param float  $timeout 指定超时时间，默认为-1，表示永不超时，会持续等待直到有其他进程唤醒
     */
    public static function wait(string $name, float $timeout = -1): bool
    {
        return static::getInstance($name)->wait($timeout);
    }

    /**
     * 唤醒处于wait状态的其他进程。
     * 当前原子计数如果为0时，表示没有进程正在wait，wakeup会立即返回true
     * 当前原子计数如果为1时，表示当前有进程正在wait，wakeup会唤醒等待的进程，并返回true
     * 如果同时有多个进程处于wait状态，$n参数可以控制唤醒的进程数量
     * 被唤醒的进程返回后，会将原子计数设置为0，这时可以再次调用wakeup唤醒其他正在wait的进程.
     *
     * @param string $name 原子计数对象名称
     */
    public static function wakeup(string $name, int $n = 1): bool
    {
        return static::getInstance($name)->wakeup($n);
    }
}
