<?php

declare(strict_types=1);

namespace Imi\Cron\Message;

use Imi\App;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Worker;

class Result implements IMessage
{
    /**
     * 进程类型.
     */
    public string $processType = '';

    /**
     * 进程标识符.
     */
    public ?string $processId = null;

    public function __construct(/**
     * 动作.
     */
    public string $action, /**
     * 定时任务ID.
     */
    public string $id, /**
     * 是否成功
     */
    public bool $success, /**
     * 消息.
     */
    public string $message)
    {
        $this->processType = App::get(ProcessAppContexts::PROCESS_TYPE);
        $this->processId = match ($this->processType) {
            ProcessType::WORKER, ProcessType::TASK_WORKER => (string) Worker::getWorkerId(),
            ProcessType::PROCESS => App::get(ProcessAppContexts::PROCESS_NAME) . '#' . Worker::getWorkerId(),
            default => throw new \InvalidArgumentException(sprintf('Invalid process type %s', $this->processType)),
        };
    }
}
