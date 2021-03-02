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
     *
     * @param \Imi\Cron\Annotation\Cron $cron
     * @param string                    $pointClass
     *
     * @return void
     */
    public function addCronByAnnotation(Cron $cron, string $pointClass);

    /**
     * 增加定时任务
     *
     * @param string          $id
     * @param string|null     $type
     * @param callable|string $task
     * @param array           $cronRules
     * @param mixed           $data
     * @param float           $lockExpire
     * @param string|null     $unique
     * @param string|null     $redisPool
     * @param float           $lockWaitTimeout
     * @param bool            $force
     *
     * @return void
     */
    public function addCron(string $id, ?string $type, $task, array $cronRules, $data, float $lockExpire = 3, ?string $unique = null, ?string $redisPool = null, float $lockWaitTimeout = 3, bool $force = false);

    /**
     * 移除定时任务
     *
     * @param string $id
     *
     * @return void
     */
    public function removeCron(string $id);

    /**
     * 清空定时任务
     *
     * @return void
     */
    public function clear();

    /**
     * Get 真实的任务对象列表.
     *
     * @return \Imi\Cron\CronTask[]
     */
    public function getRealTasks(): array;

    /**
     * 获取任务对象
     *
     * @param string $id
     *
     * @return \Imi\Cron\CronTask|null
     */
    public function getTask($id): ?CronTask;

    /**
     * 获取任务回调.
     *
     * @param string      $cronId
     * @param string      $class
     * @param string|null $cronType
     *
     * @return string|callable
     */
    public function getTaskCallable(string $cronId, string $class, ?string &$cronType);

    /**
     * socket 文件路径.
     *
     * @return string
     */
    public function getSocketFile(): string;

    /**
     * 使用类名获取定时任务类型.
     *
     * @param string $class
     *
     * @return string|null
     */
    public function getCronTypeByClass(string $class): ?string;
}
