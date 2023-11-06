<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Task;

use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

#[Task(name: 'Test1')]
class TestTask implements ITaskHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId): mixed
    {
        $data = $param->getData();

        return date('Y-m-d H:i:s', $data['time']);
    }

    /**
     * {@inheritDoc}
     */
    public function finish(\Swoole\Server $server, int $taskId, mixed $data): void
    {
    }
}
