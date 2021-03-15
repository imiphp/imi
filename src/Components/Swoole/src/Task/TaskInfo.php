<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Swoole\Task\Interfaces\ITaskHandler;

class TaskInfo
{
    /**
     * 任务执行器.
     *
     * @var \Imi\Swoole\Task\Interfaces\ITaskHandler
     */
    private ITaskHandler $taskHandler;

    /**
     * 任务参数.
     *
     * @var TaskParam
     */
    private TaskParam $param;

    public function __construct(ITaskHandler $taskHandler, TaskParam $param)
    {
        $this->taskHandler = $taskHandler;
        $this->param = $param;
    }

    /**
     * Get the value of taskHandler.
     *
     * @return \Imi\Swoole\Task\Interfaces\ITaskHandler
     */
    public function getTaskHandler(): ITaskHandler
    {
        return $this->taskHandler;
    }

    /**
     * Get the value of param.
     *
     * @return TaskParam
     */
    public function getParam(): TaskParam
    {
        return $this->param;
    }
}
