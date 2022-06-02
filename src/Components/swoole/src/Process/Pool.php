<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\App;
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

        Signal::waitCallback(\SIGCHLD, function () {
            if ($this->workers)
            {
                while (Process::wait(false))
                {
                }
            }
        });

        imigo(function () {
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
                            // @phpstan-ignore-next-line
                            App::getBean('ErrorLog')->onException($th);
                        }
                    }
                    $time = microtime(true);
                    while ($this->workers)
                    {
                        if (microtime(true) - $time > 3)
                        {
                            Log::info('Worker exit timeout, forced to terminate');
                            foreach ($this->workers as $key => $worker)
                            {
                                try
                                {
                                    Process::kill($worker->pid, \SIGKILL);
                                }
                                catch (\Throwable $th)
                                {
                                    // @phpstan-ignore-next-line
                                    App::getBean('ErrorLog')->onException($th);
                                }
                            }
                            break;
                        }
                        foreach ($this->workers as $key => $worker)
                        {
                            if (!Process::kill($worker->pid, 0))
                            {
                                unset($this->workers[$key]);
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

        $this->trigger('Init', [
            'pool'      => $this,
        ], $this, InitEventParam::class);
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
            try
            {
                Process::kill($worker->pid);
            }
            catch (\Throwable $th)
            {
                // @phpstan-ignore-next-line
                App::getBean('ErrorLog')->onException($th);
            }
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
                try
                {
                    Process::kill($workers[$workerId]->pid);
                }
                catch (\Throwable $th)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($th);
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
        $oldEnableDeadlockCheck = Coroutine::getOptions()['enable_deadlock_check'] ?? true;
        Coroutine::set([
            'enable_deadlock_check' => false,
        ]);
        $worker = new Process(function (Process $worker) use ($workerId) {
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
        }
    }
}
