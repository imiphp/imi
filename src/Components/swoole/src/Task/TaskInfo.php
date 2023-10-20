<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Swoole\Task\Interfaces\ITaskHandler;

class TaskInfo
{
    public function __construct(
        /**
         * 任务执行器.
         */
        private readonly ITaskHandler $taskHandler,
        /**
         * 任务参数.
         */
        private readonly TaskParam $param
    ) {
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
