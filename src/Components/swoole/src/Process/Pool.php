<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\Event\TEvent;
use Imi\Log\Log;
use Imi\Swoole\Process\Pool\BeforeStartEventParam;
use Imi\Swoole\Process\Pool\InitEventParam;
use Imi\Swoole\Process\Pool\MessageEventParam;
use Imi\Swoole\Process\Pool\WorkerEventParam;
use Imi\Swoole\Util\Coroutine;
use Swoole\Event;

class Pool
{
    use TEvent;

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
    private int $masterPID = 0;

    public function __construct(private readonly int $workerNum)
    {
    }

    protected function listenSigChild(): void
    {
        Signal::waitCallback(\SIGCHLD, function (): void {
            if ($this->workers)
            {
                while ($result = Process::wait(false))
                {
                    $pid = $result['pid'];
                    $workerId = $this->workerIdMap[$pid] ?? null;
                    if (isset($this->workers[$workerId]))
                    {
                        $worker = $this->workers[$workerId];
                        Event::del($worker->pipe);
                        unset($this->workerIdMap[$worker->pid], $this->workers[$workerId]);
                    }
                }
            }
            if ($this->working)
            {
                $this->listenSigChild();
            }
        });
    }

    /**
     * 启动进程池.
     */
    public function start(): void
    {
        $this->masterPID = getmypid();
        $this->working = true;

        $this->dispatch(new BeforeStartEventParam($this));

        $this->listenSigChild();

        imigo(function (): void {
            if (Signal::wait(\SIGTERM))
            {
                if ($this->workers)
                {
                    $this->working = false;
                    foreach ($this->workers as $worker)
                    {
                        try
                        {
                            Process::kill($worker->pid, \SIGTERM);
                            if (Event::isset($worker->pipe))
                            {
                                Event::del($worker->pipe);
                            }
                        }
                        catch (\Throwable $th)
                        {
                            Log::error($th);
                        }
                    }
                    $time = microtime(true);
                    while ($this->workers)
                    {
                        if (microtime(true) - $time > 3)
                        {
                            Log::info('Worker exit timeout, forced to terminate');
                            foreach ($this->workers as $worker)
                            {
                                try
                                {
                                    Process::kill($worker->pid, \SIGKILL);
                                }
                                catch (\Throwable $th)
                                {
                                    Log::error($th);
                                }
                            }
                            break;
                        }
                        foreach ($this->workers as $worker)
                        {
                            if ($result = Process::wait(false))
                            {
                                $pid = $result['pid'];
                                $workerId = $this->workerIdMap[$pid] ?? null;
                                if (isset($this->workers[$workerId]))
                                {
                                    $worker = $this->workers[$workerId];
                                    Event::del($worker->pipe);
                                    unset($this->workerIdMap[$worker->pid], $this->workers[$workerId]);
                                }
                            }
                        }
                        usleep(10000);
                    }
                }
            }
        });

        for ($i = 0; $i < $this->workerNum; ++$i)
        {
            $this->startWorker($i);
        }

        $this->dispatch(new InitEventParam($this));
    }

    public function wait(bool $blocking = true): bool
    {
        $result = true;
        while ($this->working)
        {
            $result = true;
            for ($i = 0; $i < $this->workerNum; ++$i)
            {
                if (isset($this->workers[$i]))
                {
                    $worker = $this->workers[$i];
                    if (Process::kill($worker->pid, 0))
                    {
                        $result = false;
                    }
                    else
                    {
                        Event::del($worker->pipe);
                        unset($this->workerIdMap[$worker->pid], $this->workers[$i]);
                    }
                }
                else
                {
                    $this->startWorker($i);
                }
            }
            if ($blocking)
            {
                usleep(10000);
            }
            else
            {
                break;
            }
        }

        return $result;
    }

    /**
     * 停止工作池.
     */
    public function shutdown(): void
    {
        Process::kill($this->masterPID, \SIGTERM);
    }

    /**
     * 重启所有工作进程.
     */
    public function restartAllWorker(): void
    {
        foreach ($this->workers as $worker)
        {
            try
            {
                Process::kill($worker->pid);
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        }
    }

    /**
     * 重启指定工作进程.
     *
     * @param int ...$workerIds
     */
    public function restartWorker(...$workerIds): void
    {
        $workers = &$this->workers;
        foreach ($workerIds as $workerId)
        {
            if (isset($workers[$workerId]))
            {
                try
                {
                    Process::kill($workers[$workerId]->pid);
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
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
     */
    private function startWorker(int $workerId): void
    {
        $workers = &$this->workers;
        if (isset($workers[$workerId]))
        {
            throw new \RuntimeException(sprintf('Can not start worker %s again', $workerId));
        }
        $oldEnableDeadlockCheck = Coroutine::getOptions()['enable_deadlock_check'] ?? true;
        Coroutine::set([
            'enable_deadlock_check' => false,
        ]);
        $worker = new Process(function (Process $worker) use ($workerId): void {
            Process::signal(\SIGTERM, function () use ($worker, $workerId): void {
                $this->dispatch(new WorkerEventParam('WorkerExit', $this, $worker, $workerId));
                Event::exit();
            });
            register_shutdown_function(function () use ($worker, $workerId): void {
                $this->dispatch(new WorkerEventParam('WorkerStop', $this, $worker, $workerId));
            });
            $this->dispatch(new WorkerEventParam('WorkerStart', $this, $worker, $workerId));
            Event::wait();
        });
        $pid = $worker->start();
        Coroutine::set([
            'enable_deadlock_check' => $oldEnableDeadlockCheck,
        ]);
        if (false === $pid)
        {
            throw new \RuntimeException(sprintf('Start worker %s failed', $workerId));
        }
        else
        {
            $workers[$workerId] = $worker;
            $this->workerIdMap[$pid] = $workerId;

            Event::add($worker->pipe, function ($pipe) use ($worker, $workerId): void {
                $content = $worker->read();
                if (false === $content || '' === $content)
                {
                    Log::warning('%s: Read pipe message content failed');

                    return;
                }
                $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
                $this->dispatch(new MessageEventParam($this, $worker, $workerId, $data));
            });
        }
    }
}
