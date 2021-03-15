<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Base;
use Swoole\Server\Task;

class TaskEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Swoole\Server\Base
     */
    public Base $server;

    /**
     * 任务ID.
     *
     * @var int
     */
    public int $taskId = 0;

    /**
     * worker进程ID.
     *
     * @var int
     */
    public int $workerId = 0;

    /**
     * 任务数据.
     *
     * @var mixed
     */
    public $data;

    /**
     * 任务的类型 taskwait,task,taskCo,taskWaitMulti 可能使用不同的 flags.
     *
     * @var mixed
     */
    public $flags;

    /**
     * task 对象
     *
     * @var \Swoole\Server\Task|null
     */
    public ?Task $task = null;
}
