<?php

declare(strict_types=1);

namespace Imi\Workerman\Cron\Process;

use Imi\Aop\Annotation\Inject;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\IScheduler;
use Imi\Cron\CronManager;
use Imi\Cron\Message\AddCron;
use Imi\Cron\Message\Clear;
use Imi\Cron\Message\RemoveCron;
use Imi\Cron\Message\Result;
use Imi\Log\ErrorLog;
use Imi\Timer\Timer;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\BaseProcess;
use Imi\Workerman\Server\WorkermanServerWorker;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

/**
 * 定时任务进程.
 *
 * @Process(name="CronProcess")
 */
class CronProcess extends BaseProcess
{
    /**
     * @Inject("CronScheduler")
     */
    protected IScheduler $scheduler;

    /**
     * @Inject("ErrorLog")
     */
    protected ErrorLog $errorLog;

    /**
     * @Inject("CronManager")
     */
    protected ICronManager $cronManager;

    /**
     * socket 资源.
     *
     * @var resource
     */
    protected $socket;

    /**
     * 是否正在运行.
     */
    protected bool $running = false;

    /**
     * Unix 服务器.
     */
    private Worker $unixWorker;

    public function run(Worker $worker): void
    {
        $this->startSocketServer();
    }

    protected function startSocketServer(): void
    {
        $socketFile = $this->cronManager->getSocketFile();
        if (is_file($socketFile))
        {
            unlink($socketFile);
        }
        $this->unixWorker = $worker = new WorkermanServerWorker('unix://' . $socketFile);
        $worker->protocol = \Imi\Workerman\Cron\Protocol\Frame::class;
        $worker->onMessage = [$this, 'onUnixMessage'];
        $worker->listen();
        $this->startSchedule();
    }

    /**
     * @param mixed $data
     */
    public function onUnixMessage(ConnectionInterface $connection, $data): void
    {
        $result = unserialize($data);
        if ($result instanceof Result)
        {
            $this->scheduler->completeTask($result);
        }
        elseif ($result instanceof AddCron)
        {
            $cronAnnotation = $result->cronAnnotation;
            $this->cronManager->addCronByAnnotation($cronAnnotation, $result->task);
        }
        elseif ($result instanceof RemoveCron)
        {
            $this->cronManager->removeCron($result->id);
        }
        elseif ($result instanceof Clear)
        {
            $this->cronManager->clear();
        }
    }

    /**
     * 开始定时任务调度.
     */
    protected function startSchedule(): void
    {
        Timer::tick(1000, function () {
            $scheduler = $this->scheduler;
            foreach ($scheduler->schedule() as $task)
            {
                $scheduler->runTask($task);
            }
        });
    }

    /**
     * 停止.
     */
    protected function stop(): void
    {
        $this->running = false;
        $this->scheduler->close();
    }

    public function getUnixWorker(): Worker
    {
        return $this->unixWorker;
    }
}
