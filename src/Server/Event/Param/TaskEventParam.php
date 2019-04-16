<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Swoole\Server\Task;

class TaskEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 任务ID
     * @var int
     */
    public $taskID;

    /**
     * worker进程ID
     * @var int
     */
    public $workerID;

    /**
     * 任务数据
     * @var mixed
     */
    public $data;

    /**
     * 是否为协程环境
     * @var bool
     */
    public $co;

    /**
     * 任务对象
     * @var Task
     */
    public $task;
}