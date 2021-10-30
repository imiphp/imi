<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\IScheduler;
use Imi\Cron\CronCalculator;
use Imi\Cron\CronLock;
use Imi\Cron\CronTask;
use Imi\Cron\Message\Result;
use Imi\Log\Log;
use Imi\Server\ServerManager;
use Imi\Swoole\Cron\Consts\CronTaskType;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Yurun\Swoole\CoPool\CoPool;
use Yurun\Swoole\CoPool\Interfaces\ICoTask;
use Yurun\Swoole\CoPool\Interfaces\ITaskParam;

/**
 * @Bean(name="CronScheduler", recursion=false)
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
     */
    protected CronCalculator $cronCalculator;

    /**
     * @Inject("CronLock")
     */
    protected CronLock $cronLock;

    /**
     * 协程工作池的协程数量.
     */
    protected int $poolCoCount = 16;

    /**
     * 协程工作池的队列长度.
     */
    protected int $poolQueueLength = 1024;

    /**
     * 协程工作池.
     */
    private CoPool $coPool;

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

    public function __construct()
    {
        $this->coPool = $coPool = new CoPool($this->poolCoCount, $this->poolQueueLength,
            // 定义任务匿名类，当然你也可以定义成普通类，传入完整类名
            // @phpstan-ignore-next-line
            new class() implements ICoTask {
                /**
                 * {@inheritDoc}
                 */
                public function run(ITaskParam $param)
                {
                    /** @var \Imi\Cron\CronTask $task */
                    $task = $param->getData();
                    /** @var \Imi\Cron\CronManager $cronManager */
                    $cronManager = App::getBean('CronManager');
                    switch ($type = $task->getType())
                    {
                        case CronTaskType::RANDOM_WORKER:
                            /** @var ISwooleServer $server */
                            $server = ServerManager::getServer('main', ISwooleServer::class);
                            $swooleServer = $server->getSwooleServer();
                            $taskClass = $task->getTask();
                            $swooleServer->sendMessage(json_encode([
                                'action'    => 'cronTask',
                                'id'        => $task->getId(),
                                'data'      => $task->getData(),
                                'task'      => \is_callable($taskClass) ? null : $taskClass,
                                'type'      => $type,
                            ], \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE), mt_rand(0, $swooleServer->setting['worker_num'] - 1));
                            break;
                        case CronTaskType::ALL_WORKER:
                            /** @var ISwooleServer $server */
                            $server = ServerManager::getServer('main', ISwooleServer::class);
                            $swooleServer = $server->getSwooleServer();
                            $taskClass = $task->getTask();
                            $message = json_encode([
                                'action'    => 'cronTask',
                                'id'        => $task->getId(),
                                'data'      => $task->getData(),
                                'task'      => \is_callable($taskClass) ? null : $taskClass,
                                'type'      => $type,
                            ], \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
                            for ($i = 0; $i < $swooleServer->setting['worker_num']; ++$i)
                            {
                                $swooleServer->sendMessage($message, $i);
                            }
                            break;
                        case CronTaskType::TASK:
                            $cronManager->getTaskCallable($task->getId(), $task->getTask(), $type)($task->getId(), $task->getData());
                            break;
                        case CronTaskType::PROCESS:
                            $cronManager->getTaskCallable($task->getTask(), $task->getTask(), $type)($task->getId(), $task->getData());
                            break;
                        case CronTaskType::CRON_PROCESS:
                            /** @var \Imi\Cron\CronWorker $cronWorker */
                            $cronWorker = App::getBean('CronWorker');
                            $cronWorker->exec($task->getId(), $task->getData(), $task->getTask(), $type);
                            break;
                    }
                }
            }
        );
        // 运行协程池
        $coPool->run();
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->coPool->stop();
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
        $this->coPool->addTaskAsync($task);
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
