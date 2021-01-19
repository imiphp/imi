<?php

declare(strict_types=1);

namespace Imi\Workerman\Worker;

use Imi\Bean\Annotation\Bean;
use Imi\Server\ServerManager;
use Imi\Workerman\Contract\IWorkermanWorker;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Workerman\Worker;

/**
 * @Bean("WorkermanWorkerHandler")
 */
class WorkermanWorkerHandler implements IWorkermanWorker
{
    /**
     * 是否初始化完毕.
     *
     * @return bool
     */
    private bool $isInited = false;

    /**
     * Workerman 的 Worker 对象
     *
     * @var Worker|null
     */
    private ?Worker $worker = null;

    /**
     * 获取 Workerman 的 Worker 对象
     *
     * @return \Workerman\Worker
     */
    public function getWorker(): Worker
    {
        if (null === $this->worker)
        {
            $servers = ServerManager::getServers();
            /** @var IWorkermanServer $server */
            $server = reset($servers);

            return $this->worker = $server->getWorker();
        }
        else
        {
            return $this->worker;
        }
    }

    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     *
     * @return int|null
     */
    public function getWorkerId(): ?int
    {
        return $this->getWorker()->id;
    }

    /**
     * 获取 Worker 进程数量.
     *
     * @return int
     */
    public function getWorkerNum(): int
    {
        return $this->getWorker()->count;
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
    }
}
