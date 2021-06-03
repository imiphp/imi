<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Swoole\Task\Interfaces\ITaskHandler;

class TaskInfo
{
    /**
     * 任务执行器.
     */
    private ITaskHandler $taskHandler;

    /**
     * 任务参数.
     */
    private TaskParam $param;

    public function __construct(ITaskHandler $taskHandler, TaskParam $param)
    {
        $this->taskHandler = $taskHandler;
        $this->param = $param;
    }

    public function getTaskHandler(): ITaskHandler
    {
        return $this->taskHandler;
    }

    public function getParam(): TaskParam
    {
        return $this->param;
    }
}
