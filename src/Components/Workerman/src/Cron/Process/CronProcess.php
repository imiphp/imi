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
     *
     * @var \Imi\Cron\Contract\IScheduler
     */
    protected IScheduler $scheduler;

    /**
     * @Inject("ErrorLog")
     *
     * @var \Imi\Log\ErrorLog
     */
    protected ErrorLog $errorLog;

    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\Contract\ICronManager
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
     *
     * @var bool
     */
    protected bool $running = false;

    /**
     * Unix 服务器.
     *
     * @var Worker
     */
    private Worker $unixWorker;

    public function run(Worker $worker)
    {
        $this->startSocketServer();
    }

    protected function startSocketServer()
    {
        $socketFile = $this->cronManager->getSocketFile();
        if (is_file($socketFile))
        {
            unlink($socketFile);
        }
        $this->unixWorker = $worker = new Worker('unix://' . $socketFile);
        $worker->protocol = \Imi\Workerman\Cron\Protocol\Frame::class;
        $worker->onMessage = [$this, 'onUnixMessage'];
        $worker->listen();
        $this->startSchedule();
    }

    public function onUnixMessage(ConnectionInterface $connection, $data)
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
     *
     * @return void
     */
    protected function startSchedule()
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
     *
     * @return void
     */
    protected function stop()
    {
        $this->running = false;
        $this->scheduler->close();
    }
}
