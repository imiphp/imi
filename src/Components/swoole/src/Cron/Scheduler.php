<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Cron\CronTask;
use Imi\Log\Log;
use Imi\Server\ServerManager;
use Imi\Swoole\Cron\Consts\CronTaskType;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Yurun\Swoole\CoPool\CoPool;
use Yurun\Swoole\CoPool\Interfaces\ICoTask;
use Yurun\Swoole\CoPool\Interfaces\ITaskParam;

/**
 * 定时任务调度器.
 */
#[Bean(name: 'CronScheduler')]
class Scheduler extends \Imi\Cron\Scheduler
{
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
    private readonly CoPool $coPool;

    public function __construct()
    {
        parent::__construct();
        $this->coPool = $coPool = new CoPool($this->poolCoCount, $this->poolQueueLength,
            // 定义任务匿名类，当然你也可以定义成普通类，传入完整类名
            // @phpstan-ignore-next-line
            new class() implements ICoTask {
                /**
                 * {@inheritDoc}
                 */
                public function run(ITaskParam $param): void
                {
                    /** @var \Imi\Cron\CronTask $task */
                    $task = $param->getData();
                    /** @var \Imi\Cron\CronManager $cronManager */
                    $cronManager = App::getBean('CronManager');
                    switch ($type = $task->getType())
                    {
                        case CronTaskType::RANDOM_WORKER:
                            $swooleServer = ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer();
                            $taskClass = $task->getTask();
                            $swooleServer->sendMessage(json_encode([
                                'action'    => 'cronTask',
                                'id'        => $task->getId(),
                                'data'      => $task->getData(),
                                'task'      => \is_callable($taskClass) ? null : $taskClass,
                                'type'      => $type,
                            ], \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE), random_int(0, $swooleServer->setting['worker_num'] - 1));
                            break;
                        case CronTaskType::ALL_WORKER:
                            $swooleServer = ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer();
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
        parent::close();
        $this->coPool->stop();
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
}
