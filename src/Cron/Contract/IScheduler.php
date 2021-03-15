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
     *
     * @return void
     */
    public function close();

    /**
     * 遍历可运行的任务列表.
     *
     * @return \Generator
     */
    public function schedule(): \Generator;

    /**
     * 执行任务
     *
     * @param \Imi\Cron\CronTask $task
     *
     * @return void
     */
    public function runTask(CronTask $task);

    /**
     * 完成任务
     *
     * @param \Imi\Cron\Message\Result $result
     *
     * @return void
     */
    public function completeTask(Result $result);
}
