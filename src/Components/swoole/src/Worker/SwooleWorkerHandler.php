<?php

declare(strict_types=1);

namespace Imi\Swoole\Worker;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Server\Event\ServerEvents;
use Imi\Server\Event\WorkerStartEvent;
use Imi\Server\ServerManager;
use Imi\Swoole\Contract\ISwooleWorker;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Swoole\Util\Coroutine;

use function Swoole\Coroutine\defer;

#[Bean(name: 'SwooleWorkerHandler')]
class SwooleWorkerHandler implements ISwooleWorker
{
    /**
     * 当前进程的WorkerId.
     */
    private ?int $workerId = null;

    /**
     * 是否初始化完毕.
     */
    private bool $isInited = false;

    /**
     * imi.main_server.worker.start.app 事件执行完毕.
     */
    private bool $workerStartAppComplete = false;

    /**
     * Worker 进程数量.
     */
    private ?int $workerNum = null;

    /**
     * task 进程数量.
     */
    private ?int $taskWorkerNum = null;

    /**
     * {@inheritDoc}
     */
    public function getWorkerId(): ?int
    {
        if (null === $this->workerId)
        {
            /** @var ISwooleServer|null $main */
            $main = ServerManager::getServer('main', ISwooleServer::class);
            if (!$main)
            {
                return null;
            }
            $workerId = $main->getSwooleServer()->worker_id;
            if ($workerId > -1)
            {
                $this->workerId = $workerId;
            }
        }

        return $this->workerId;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkerNum(): int
    {
        if (!$this->workerNum)
        {
            $this->workerNum = (int) ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer()->setting['worker_num'];
        }

        return $this->workerNum;
    }

    /**
     * {@inheritDoc}
     */
    public function isInited(): bool
    {
        return $this->isInited;
    }

    /**
     * {@inheritDoc}
     */
    public function inited(): void
    {
        $this->isInited = true;
        // 触发 imi.main_server.worker.start.app 事件.
        if (!$this->workerStartAppComplete)
        {
            $mainServer = ServerManager::getServer('main', ISwooleServer::class);
            // 触发项目的workerstart事件
            Event::dispatch(new WorkerStartEventParam(SwooleEvents::WORKER_APP_START, $mainServer, $this->workerId));
            Event::dispatch(new WorkerStartEvent(ServerEvents::WORKER_APP_START, $mainServer, $this->workerId));
            $this->workerStartAppComplete = true;
        }
        $func = static function (): void {
            if (ChannelContainer::hasChannel('workerInit'))
            {
                ChannelContainer::removeChannel('workerInit');
            }
        };
        if (Coroutine::isIn())
        {
            defer($func);
        }
        else
        {
            $func();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isWorkerStartAppComplete(): bool
    {
        return $this->workerStartAppComplete;
    }

    /**
     * {@inheritDoc}
     */
    public function getTaskWorkerNum(): int
    {
        if (!$this->taskWorkerNum)
        {
            $this->taskWorkerNum = (int) ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer()->setting['task_worker_num'];
        }

        return $this->taskWorkerNum;
    }

    /**
     * {@inheritDoc}
     */
    public function isTask(): bool
    {
        return ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer()->taskworker;
    }

    /**
     * {@inheritDoc}
     */
    public function getMasterPid(): int
    {
        $swooleServer = ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer();

        if (\SWOOLE_BASE === $swooleServer->mode)
        {
            return $this->getManagerPid();
        }
        else
        {
            return $swooleServer->master_pid;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getManagerPid(): int
    {
        return ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer()->manager_pid;
    }

    /**
     * {@inheritDoc}
     */
    public function isWorkerIdProcess(int $workerId): bool
    {
        return $workerId >= $this->getWorkerNum() + $this->getTaskWorkerNum();
    }
}
