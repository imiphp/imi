<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\Interfaces\ITaskParam;

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
     * @var \Imi\Swoole\Task\Interfaces\ITaskParam
     */
    private ITaskParam $param;

    public function __construct(ITaskHandler $taskHandler, ITaskParam $param)
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
     * @return \Imi\Swoole\Task\Interfaces\ITaskParam
     */
    public function getParam(): ITaskParam
    {
        return $this->param;
    }
}
