<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Swoole\Server\Task;

class TaskEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 服务器对象
         */
        public readonly ?ISwooleServer $server = null,
        /**
         * 任务ID.
         */
        public readonly int $taskId = 0,
        /**
         * worker进程ID.
         */
        public readonly int $workerId = 0,
        /**
         * 任务数据.
         */
        public readonly mixed $data = null,
        /**
         * 任务的类型 taskwait,task,taskCo,taskWaitMulti 可能使用不同的 flags.
         */
        public readonly mixed $flags = null,
        /**
         * task 对象
         */
        public readonly ?Task $task = null
    ) {
        parent::__construct(SwooleEvents::SERVER_TASK, $server);
    }
}
