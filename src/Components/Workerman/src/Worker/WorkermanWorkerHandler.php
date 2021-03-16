<?php

declare(strict_types=1);

namespace Imi\Workerman\Worker;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Workerman\Contract\IWorkermanWorker;
use Imi\Workerman\Server\WorkermanServerWorker;
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
     */
    private ?Worker $worker = null;

    /**
     * 获取 Workerman 的 Worker 对象
     */
    public function getWorker(): Worker
    {
        if (null === $this->worker)
        {
            return $this->worker = RequestContext::get('worker');
        }
        else
        {
            return $this->worker;
        }
    }

    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     */
    public function getWorkerId(): ?int
    {
        return $this->getWorker()->id;
    }

    /**
     * 获取 Worker 进程数量.
     */
    public function getWorkerNum(): int
    {
        return $this->getWorker()->count;
    }

    /**
     * 是否初始化完毕.
     */
    public function isInited(): bool
    {
        return $this->isInited;
    }

    /**
     * 初始化完毕.
     */
    public function inited(): void
    {
        $this->isInited = true;
    }

    /**
     * 获取服务器 master 进程 PID.
     */
    public function getMasterPid(): int
    {
        return WorkermanServerWorker::getMasterPid();
    }
}
