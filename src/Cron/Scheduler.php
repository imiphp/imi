<?php

declare(strict_types=1);

namespace Imi\Cron;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Cron\Consts\CronTaskType;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\IScheduler;
use Imi\Cron\Message\Result;
use Imi\Log\Log;

/**
 * @Bean("CronScheduler")
 *
 * 定时任务调度器
 */
class Scheduler implements IScheduler
{
    /**
     * @Inject("CronManager")
     */
    protected ICronManager $cronManager;

    /**
     * @Inject("CronCalculator")
     *
     * @var \Imi\Cron\CronCalculator
     */
    protected CronCalculator $cronCalculator;

    /**
     * @Inject("CronLock")
     *
     * @var \Imi\Cron\CronLock
     */
    protected CronLock $cronLock;

    /**
     * 下次执行时间集合.
     */
    private array $nextTickTimeMap = [];

    /**
     * 正在执行的任务列表.
     *
     * @var \Imi\Cron\CronTask[]
     */
    private array $runningTasks = [];

    /**
     * 首次执行记录集合.
     */
    private array $firstRunMap = [];

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function schedule(): \Generator
    {
        $now = time();
        $runningTasks = &$this->runningTasks;
        $nextTickTimeMap = &$this->nextTickTimeMap;
        $cronCalculator = $this->cronCalculator;
        $firstRunMap = &$this->firstRunMap;
        foreach ($this->cronManager->getRealTasks() as $task)
        {
            $id = $task->getId();
            if (isset($runningTasks[$id]))
            {
                if (time() < $task->getLastRunTime() + $task->getMaxExecutionTime())
                {
                    continue;
                }
                else
                {
                    unset($runningTasks[$id]);
                }
            }
            $nextTickTimeMap[$id] ??= $cronCalculator->getNextTickTime($task->getLastRunTime(), $task->getCronRules());
            $firstRun = !isset($firstRunMap[$id]) && $task->getForce();
            if ($firstRun || $now >= $nextTickTimeMap[$id])
            {
                if ($firstRun)
                {
                    $firstRunMap[$id] = true;
                }
                else
                {
                    unset($nextTickTimeMap[$id]);
                }
                yield $task;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function runTask(CronTask $task): void
    {
        if (!$this->cronLock->lock($task))
        {
            Log::error(sprintf('Task %s lock failed', $task->getId()));

            return;
        }
        $task->updateLastRunTime(time());
        $this->runningTasks[$task->getId()] = $task;
        // 执行
        switch ($type = $task->getType())
        {
            case CronTaskType::CRON_PROCESS:
            /** @var \Imi\Cron\CronWorker $cronWorker */
            $cronWorker = App::getBean('CronWorker');
            $cronWorker->exec($task->getId(), $task->getData(), $task->getTask(), $type);
            break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function completeTask(Result $result): void
    {
        $runningTasks = &$this->runningTasks;
        $resultId = $result->id;
        if (isset($runningTasks[$resultId]))
        {
            if (!$this->cronLock->unlock($runningTasks[$resultId]))
            {
                Log::error(sprintf('Task %s unlock failed', $resultId));
            }
            unset($runningTasks[$resultId]);
        }
        if ($result->success)
        {
            Log::info(sprintf('Task: %s, Process: %s#%s, Success', $resultId, $result->processType, $result->processId));
        }
        else
        {
            Log::error(sprintf('Task: %s, Process: %s#%s, %s', $resultId, $result->processType, $result->processId, $result->message));
        }
    }
}
