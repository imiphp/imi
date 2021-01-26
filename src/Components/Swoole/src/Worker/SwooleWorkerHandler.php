<?php

declare(strict_types=1);

namespace Imi\Swoole\Worker;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Server\ServerManager;
use Imi\Swoole\Contract\ISwooleWorker;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;

/**
 * @Bean("SwooleWorkerHandler")
 */
class SwooleWorkerHandler implements ISwooleWorker
{
    /**
     * 当前进程的WorkerId.
     *
     * @var int|null
     */
    private ?int $workerId = null;

    /**
     * 是否初始化完毕.
     *
     * @return bool
     */
    private bool $isInited = false;

    /**
     * IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     *
     * @var bool
     */
    private bool $workerStartAppComplete = false;

    /**
     * Worker 进程数量.
     *
     * @var int|null
     */
    private ?int $workerNum = null;

    /**
     * task 进程数量.
     *
     * @var int|null
     */
    private ?int $taskWorkerNum = null;

    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     *
     * @return int|null
     */
    public function getWorkerId(): ?int
    {
        if (null === $this->workerId)
        {
            $main = ServerManager::getServer('main', ISwooleServer::class);
            if ($main instanceof \Imi\Swoole\Server\Base)
            {
                $workerId = $main->getSwooleServer()->worker_id;
                if ($workerId > -1)
                {
                    $this->workerId = $workerId;
                }
            }
            elseif ($main instanceof \Imi\Swoole\Server\CoServer)
            {
                $this->workerId = $main->getWorkerId();
            }
        }

        return $this->workerId;
    }

    /**
     * 获取 Worker 进程数量.
     *
     * @return int
     */
    public function getWorkerNum(): int
    {
        if (!$this->workerNum)
        {
            /** @var ISwooleServer $server */
            $server = ServerManager::getServer('main', ISwooleServer::class);
            $this->workerNum = $server->getSwooleServer()->setting['worker_num'];
        }

        return $this->workerNum;
    }

    /**
     * 是否初始化完毕.
     *
     * @return bool
     */
    public function isInited(): bool
    {
        return $this->isInited;
    }

    /**
     * 初始化完毕.
     *
     * @return void
     */
    public function inited()
    {
        $this->isInited = true;
        $mainServer = ServerManager::getServer('main', ISwooleServer::class);
        // 触发 IMI.MAIN_SERVER.WORKER.START.APP 事件.
        if (!$this->workerStartAppComplete)
        {
            // 触发项目的workerstart事件
            Event::trigger('IMI.MAIN_SERVER.WORKER.START.APP', [
                'server'    => $mainServer,
                'workerId'  => $this->workerId,
            ], $mainServer, WorkerStartEventParam::class);
            $this->workerStartAppComplete = true;
        }
    }

    /**
     * 是否 IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     *
     * @return bool
     */
    public function isWorkerStartAppComplete(): bool
    {
        return $this->workerStartAppComplete;
    }

    /**
     * 获取 task 进程数量.
     *
     * @return int
     */
    public function getTaskWorkerNum(): int
    {
        if (!$this->taskWorkerNum)
        {
            /** @var ISwooleServer $server */
            $server = ServerManager::getServer('main', ISwooleServer::class);
            $this->taskWorkerNum = $server->getSwooleServer()->setting['task_worker_num'];
        }

        return $this->taskWorkerNum;
    }

    /**
     * 是否为 task 进程.
     *
     * @return bool
     */
    public function isTask(): bool
    {
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);

        return $server->getSwooleServer()->taskworker;
    }

    /**
     * 获取服务器 master 进程 PID.
     *
     * @return int
     */
    public function getMasterPid(): int
    {
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);

        return $server->getSwooleServer()->master_pid;
    }

    /**
     * 获取服务器 manager 进程 PID.
     *
     * @return int
     */
    public function getManagerPid(): int
    {
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);

        return $server->getSwooleServer()->manager_pid;
    }
}
