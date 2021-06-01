<?php

declare(strict_types=1);

namespace Imi\RateLimit;

use Imi\RateLimit\Exception\RateLimitException;

/**
 * 并发工作数限流器手动调用类.
 */
abstract class WorkerLimiter
{
    /**
     * 限流执行任务
     *
     * @param callable    $callable 任务回调
     * @param string      $name     限流器名称
     * @param int         $max      最大同时运行任务数
     * @param float       $timeout  超时时间，单位：秒;为 null 不限制
     * @param callable    $callback 触发限流的回调
     * @param string|null $poolName 连接池名称，留空取默认 redis 连接池
     *
     * @return mixed
     */
    public static function call($callable, $name, $max, $timeout = null, $callback = null, $poolName = null)
    {
        // 加锁
        $workerId = WorkerLimiterLock::lock($name, $max, $timeout, $poolName);
        if (false === $workerId)
        {
            if ($callback)
            {
                return $callback($name);
            }
            else
            {
                return static::defaultCallback($name);
            }
        }
        // 执行任务
        $result = $callable();
        // 释放
        WorkerLimiterLock::unlock($name, $workerId, $poolName);

        return $result;
    }

    /**
     * 限流执行任务，允许超时等待.
     *
     * @param callable    $callable        任务回调
     * @param string      $name            限流器名称
     * @param int         $max             最大同时运行任务数
     * @param float       $timeout         任务超时时间，单位：秒;为 null 不限制
     * @param float       $blockingTimeout 等待重试超时时间，单位：秒;为 null 不限制
     * @param callable    $callback        触发限流的回调
     * @param string|null $poolName        连接池名称，留空取默认 redis 连接池
     *
     * @return mixed
     */
    public static function callBlock($callable, $name, $max, $timeout = null, $blockingTimeout = null, $callback = null, $poolName = null)
    {
        if (null === $blockingTimeout)
        {
            $blockingTimeout = \PHP_INT_MAX;
        }
        $isBlockingRetry = false;
        $beginBlockingRetryTime = 0;
        do
        {
            // 加锁
            $workerId = WorkerLimiterLock::lock($name, $max, $timeout, $poolName);
            if (false === $workerId)
            {
                if (!$isBlockingRetry)
                {
                    $beginBlockingRetryTime = microtime(true);
                    $isBlockingRetry = true;
                }
                $leftSleep = microtime(true) - $beginBlockingRetryTime;
                if ($leftSleep > 0)
                {
                    // 等待随机1-10毫秒数后重试
                    usleep(min(mt_rand(1000, 10000), $leftSleep * 1000));
                }
                else
                {
                    // 触发限流的回调
                    if ($callback)
                    {
                        return $callback($name);
                    }
                    else
                    {
                        return static::defaultCallback($name);
                    }
                }
                continue;
            }
            // 执行任务
            $result = $callable();
            // 释放
            WorkerLimiterLock::unlock($name, $workerId, $poolName);

            return $result;
        } while (true);
    }

    /**
     * 默认限流回调.
     *
     * @param string $name 限流器名称
     *
     * @return mixed
     */
    public static function defaultCallback($name)
    {
        throw new RateLimitException(sprintf('%s Worker Limit', $name));
    }
}
