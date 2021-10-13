<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Handler;

use Imi\App;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

class BeanTaskHandler implements ITaskHandler
{
    /**
     * 任务类类名.
     */
    private string $taskHandlerClass = '';

    public function __construct(string $taskHandlerClass)
    {
        $this->taskHandlerClass = $taskHandlerClass;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
    {
        /** @var ITaskHandler $taskHandler */
        $taskHandler = App::getBean($this->taskHandlerClass);

        return $taskHandler->handle($param, $server, $taskId, $workerId);
    }

    /**
     * {@inheritDoc}
     */
    public function finish(\Swoole\Server $server, int $taskId, $data): void
    {
        /** @var ITaskHandler $taskHandler */
        $taskHandler = App::getBean($this->taskHandlerClass);
        $taskHandler->finish($server, $taskId, $data);
    }
}
