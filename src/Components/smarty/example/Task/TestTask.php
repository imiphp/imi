<?php

declare(strict_types=1);

namespace Imi\Smarty\Example\Task;

use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;
use Imi\Task\Annotation\Task;

/**
 * @Task("Test1")
 */
class TestTask implements ITaskHandler
{
    /**
     * 任务处理方法.
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskID, int $workerID)
    {
        $data = $param->getData();

        return date('Y-m-d H:i:s', $data['time']);
    }

    /**
     * 任务结束时触发.
     *
     * @param mixed $data
     */
    public function finish(\Swoole\Server $server, int $taskId, $data): void
    {
    }
}
