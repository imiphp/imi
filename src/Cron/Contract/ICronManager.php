<?php

declare(strict_types=1);

namespace Imi\Cron\Contract;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\CronTask;

/**
 * 定时任务管理器接口.
 */
interface ICronManager
{
    /**
     * 使用注解增加定时任务
     */
    public function addCronByAnnotation(Cron $cron, string $pointClass): void;

    /**
     * 增加定时任务
     *
     * @param callable|string $task
     * @param mixed           $data
     */
    public function addCron(string $id, ?string $type, $task, array $cronRules, $data, float $lockExpire = 3, ?string $unique = null, ?string $redisPool = null, float $lockWaitTimeout = 3, bool $force = false): void;

    /**
     * 移除定时任务
     */
    public function removeCron(string $id): void;

    /**
     * 清空定时任务
     */
    public function clear(): void;

    /**
     * 获取任务对象是否存在.
     */
    public function hasTask(string $id): bool;

    /**
     * Get 真实的任务对象列表.
     *
     * @return \Imi\Cron\CronTask[]
     */
    public function getRealTasks(): array;

    /**
     * 获取任务对象
     */
    public function getTask(string $id): ?CronTask;

    /**
     * 获取任务回调.
     *
     * @return string|callable
     */
    public function getTaskCallable(string $cronId, string $class, ?string &$cronType);

    /**
     * socket 文件路径.
     */
    public function getSocketFile(): string;

    /**
     * 使用类名获取定时任务类型.
     */
    public function getCronTypeByClass(string $class): ?string;
}
