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
     * 动作.
     */
    public string $action = '';

    /**
     * 定时任务ID.
     */
    public string $id = '';

    /**
     * 进程类型.
     */
    public string $processType = '';

    /**
     * 进程标识符.
     */
    public ?string $processId = null;

    /**
     * 是否成功
     */
    public bool $success = false;

    /**
     * 消息.
     */
    public string $message = '';

    public function __construct(string $action, string $id, bool $success, string $message)
    {
        $this->action = $action;
        $this->id = $id;
        $this->success = $success;
        $this->message = $message;
        $this->processType = App::get(ProcessAppContexts::PROCESS_TYPE);
        switch ($this->processType)
        {
            case ProcessType::WORKER:
            case ProcessType::TASK_WORKER:
                $this->processId = (string) Worker::getWorkerId();
                break;
            case ProcessType::PROCESS:
                $this->processId = App::get(ProcessAppContexts::PROCESS_NAME) . '#' . Worker::getWorkerId();
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid process type %s', $this->processType));
        }
    }
}
