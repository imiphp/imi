<?php

declare(strict_types=1);

namespace Imi\Cron\Contract;

use Imi\Cron\CronTask;
use Imi\Cron\Message\Result;

/**
 * 定时任务调度器接口.
 */
interface IScheduler
{
    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * 遍历可运行的任务列表.
     */
    public function schedule(): \Generator;

    /**
     * 执行任务
     */
    public function runTask(CronTask $task): void;

    /**
     * 完成任务
     */
    public function completeTask(Result $result): void;
}
