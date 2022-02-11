<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\Event\TEvent;
use Imi\Log\Log;
use Imi\Swoole\Process\Pool\BeforeStartEventParam;
use Imi\Swoole\Process\Pool\InitEventParam;
use Imi\Swoole\Process\Pool\MessageEventParam;
use Imi\Swoole\Process\Pool\WorkerEventParam;
use Swoole\Event;
use Swoole\Process;
use Swoole\Timer;

class Pool
{
    use TEvent;

    /**
     * 工作进程数量.
     */
    private int $workerNum;

    /**
     * 工作进程列表
     * 以 WorkerId 作为 Key.
     *
     * @var \Swoole\Process[]
     */
    private array $workers = [];

    /**
     * 以进程 PID 为 key 的映射.
     *
     * @var int[]
     */
    private array $workerIdMap = [];

    /**
     * 是否工作.
     */
    private bool $working = false;

    /**
     * 主进程 PID.
     */
    private int $masterPID;

    /**
     * @param int $workerNum
     */
    public function __construct($workerNum)
    {
        $this->workerNum = $workerNum;
    }

    /**
     * 启动进程池.
     *
     * @return void
     */
    public function start()
    {
        $this->masterPID = getmypid();
        $this->working = true;

        $this->trigger('BeforeStart', [
            'pool'  => $this,
        ], $this, BeforeStartEventParam::class);

        Process::signal(\SIGCHLD, function ($sig) {
            while ($this->workers)
            {
                foreach ($this->workers as $worker)
                {
                    $ret = $worker->wait(false);
                    if ($ret)
                    {
                        $pid = $ret['pid'] ?? null;
                        $workerId = $this->workerIdMap[$pid] ?? null;
                        if (null === $workerId)
                        {
                            Log::warning(sprintf('%s: Can not found workerId by pid %s', self::class, $pid));
                            continue;
                        }
                        Event::del($this->workers[$workerId]->pipe);
                        unset($this->workerIdMap[$pid], $this->workers[$workerId]);
                        if ($this->working)
                        {
                            $this->startWorker($workerId);
                        }
                        elseif (!$this->workers)
                        {
                            Event::exit();
                        }
                    }
                }
            }
        });

        Process::signal(\SIGTERM, function () {
            $this->working = false;
            foreach ($this->workers as $worker)
            {
                Process::kill($worker->pid, \SIGTERM);
            }
            Timer::after(3000, function () {
                Log::info('Worker exit timeout, forced to terminate');
                foreach ($this->workers as $worker)
                {
                    Process::kill($worker->pid, \SIGKILL);
                }
            });
        });

        for ($i = 0; $i < $this->workerNum; ++$i)
        {
            $this->startWorker($i);
        }

        $this->trigger('Init', [
            'pool'      => $this,
        ], $this, InitEventParam::class);
    }

    /**
     * 停止工作池.
     *
     * @return void
     */
    public function shutdown()
    {
        Process::kill($this->masterPID, \SIGTERM);
    }

    /**
     * 重启所有工作进程.
     *
     * @return void
     */
    public function restartAllWorker()
    {
        foreach ($this->workers as $worker)
        {
            Process::kill($worker->pid);
        }
    }

    /**
     * 重启指定工作进程.
     *
     * @param int ...$workerIds
     *
     * @return void
     */
    public function restartWorker(...$workerIds)
    {
        $workers = &$this->workers;
        foreach ($workerIds as $workerId)
        {
            if (isset($workers[$workerId]))
            {
                Process::kill($workers[$workerId]->pid);
            }
            else
            {
                Log::warning(sprintf('%s: Can not found worker by workerId %s', self::class, $workerId));
                continue;
            }
        }
    }

    /**
     * 启动工作进程.
     *
     * @param int $workerId
     *
     * @return void
     */
    private function startWorker($workerId)
    {
        $workers = &$this->workers;
        if (isset($workers[$workerId]))
        {
            throw new \RuntimeException(sprintf('Can not start worker %s again', $workerId));
        }
        $worker = new \Imi\Swoole\Process\Process(function (Process $worker) use ($workerId) {
            Process::signal(\SIGTERM, function () use ($worker, $workerId) {
                $this->trigger('WorkerExit', [
                    'pool'      => $this,
                    'worker'    => $worker,
                    'workerId'  => $workerId,
                ], $this, WorkerEventParam::class);
                Event::exit();
            });
            register_shutdown_function(function () use ($worker, $workerId) {
                $this->trigger('WorkerStop', [
                    'pool'      => $this,
                    'worker'    => $worker,
                    'workerId'  => $workerId,
                ], $this, WorkerEventParam::class);
            });
            $this->trigger('WorkerStart', [
                'pool'      => $this,
                'worker'    => $worker,
                'workerId'  => $workerId,
            ], $this, WorkerEventParam::class);
            Event::wait();
        });
        $pid = $worker->start();
        if (false === $pid)
        {
            throw new \RuntimeException(sprintf('Start worker %s failed', $workerId));
        }
        else
        {
            Event::add($worker->pipe, function ($pipe) use ($worker, $workerId) {
                $content = $worker->read();
                if (false === $content || '' === $content)
                {
                    Log::warning('%s: Read pipe message content failed');

                    return;
                }
                $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
                $this->trigger('Message', [
                    'pool'      => $this,
                    'worker'    => $worker,
                    'workerId'  => $workerId,
                    'data'      => $data,
                ], $this, MessageEventParam::class);
            });
            $workers[$workerId] = $worker;
            $this->workerIdMap[$pid] = $workerId;
        }
    }
}
