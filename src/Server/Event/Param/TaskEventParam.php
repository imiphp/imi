<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class TaskEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 任务ID.
     *
     * @var int
     */
    public $taskID;

    /**
     * worker进程ID.
     *
     * @var int
     */
    public $workerID;

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
     * task 对象，只有 task_enable_coroutine 为 true 时可用.
     *
     * @var \Swoole\Server\Task
     */
    public $task;
}
