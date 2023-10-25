<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Process;

use Imi\Aop\Annotation\Inject;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\IScheduler;
use Imi\Cron\Message\AddCron;
use Imi\Cron\Message\Clear;
use Imi\Cron\Message\CommonMsg;
use Imi\Cron\Message\GetRealTasks;
use Imi\Cron\Message\GetTask;
use Imi\Cron\Message\HasTask;
use Imi\Cron\Message\IMessage;
use Imi\Cron\Message\RemoveCron;
use Imi\Cron\Message\Result;
use Imi\Event\Event;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;
use Imi\Swoole\Process\Event\Param\PipeMessageEventParam;
use Swoole\Coroutine\Server\Connection;

/**
 * 定时任务进程.
 */
#[Process(name: 'CronProcess')]
class CronProcess extends BaseProcess
{
    #[Inject(name: 'CronScheduler')]
    protected IScheduler $scheduler;

    #[Inject(name: 'CronManager')]
    protected ICronManager $cronManager;

    /**
     * 是否正在运行.
     */
    protected bool $running = false;

    public function run(\Swoole\Process $process): void
    {
        Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], function (): void {
            $this->stop();
        }, \Imi\Util\ImiPriority::IMI_MIN);
        Event::on('IMI.PROCESS.PIPE_MESSAGE', function (PipeMessageEventParam $e): void {
            $data = $e->data;
            if ($data instanceof Result)
            {
                $this->scheduler->completeTask($data);
            }
            elseif ($data instanceof AddCron)
            {
                $cronAnnotation = $data->cronAnnotation;
                $this->cronManager->addCronByAnnotation($cronAnnotation, $data->task);
            }
            elseif ($data instanceof RemoveCron)
            {
                $this->cronManager->removeCron($data->id);
            }
            elseif ($data instanceof Clear)
            {
                $this->cronManager->clear();
            }
            elseif ($data instanceof GetRealTasks)
            {
                // 拿到返回的数据,开启通道传回
                $this->answerClient($e->connection, $this->cronManager->getRealTasks());
            }
            elseif ($data instanceof HasTask)
            {
                // 拿到返回的数据,开启通道传回
                $this->answerClient($e->connection, $this->cronManager->hasTask($data->id));
            }
            elseif ($data instanceof GetTask)
            {
                // 拿到返回的数据,开启通道传回
                $this->answerClient($e->connection, $this->cronManager->getTask($data->id));
            }
        });
        $this->startSchedule();
    }

    /**
     * 一个返回数据的socket通道.
     *
     * @param mixed $msg
     *
     * @return int|false
     */
    protected function answerClient(Connection $conn, $msg)
    {
        if (!$msg instanceof IMessage)
        {
            $msg = new CommonMsg($msg);
        }
        $msg = serialize([
            'action' => 'cron',
            'data'   => $msg,
        ]);

        return $conn->send(pack('N', \strlen($msg)) . $msg);
    }

    /**
     * 开始定时任务调度.
     */
    protected function startSchedule(): void
    {
        $this->running = true;
        $scheduler = $this->scheduler;
        $running = &$this->running;
        do
        {
            $time = microtime(true);

            foreach ($scheduler->schedule() as $task)
            {
                $scheduler->runTask($task);
            }

            $sleep = 1 - (microtime(true) - $time);
            if ($sleep > 0)
            {
                usleep((int) ($sleep * 1000000));
            }
            else
            {
                usleep(1);
            }
        }
        // @phpstan-ignore-next-line
        while ($running);
    }

    /**
     * 停止.
     */
    protected function stop(): void
    {
        $this->running = false;
        $this->scheduler->close();
    }
}
