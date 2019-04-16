<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Swoole\Server\Task;

class TaskCoEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 任务
     * @var Task
     */
    public $task;
}