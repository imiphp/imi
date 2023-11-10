<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Handler;

use Imi\App;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

class BeanTaskHandler implements ITaskHandler
{
    public function __construct(
        /**
         * 任务类类名.
         */
        private readonly string $taskHandlerClass
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId): mixed
    {
        /** @var ITaskHandler $taskHandler */
        $taskHandler = App::getBean($this->taskHandlerClass);

        return $taskHandler->handle($param, $server, $taskId, $workerId);
    }

    /**
     * {@inheritDoc}
     */
    public function finish(\Swoole\Server $server, int $taskId, mixed $data): void
    {
        /** @var ITaskHandler $taskHandler */
        $taskHandler = App::getBean($this->taskHandlerClass);
        $taskHandler->finish($server, $taskId, $data);
    }
}
