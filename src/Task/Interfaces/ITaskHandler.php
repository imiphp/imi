<?php

namespace Imi\Task\Interfaces;

use Imi\Task\TaskParam;

interface ITaskHandler
{
    /**
     * 任务处理方法，返回的值会通过 finish 事件推送给 worker 进程.
     *
     * @param TaskParam      $param
     * @param \Swoole\Server $server
     * @param int            $taskId
     * @param int            $workerId
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId);

    /**
     * 任务结束时触发.
     *
     * @param \Swoole\Server $server
     * @param int            $taskId
     * @param mixed          $data
     *
     * @return void
     */
    public function finish(\Swoole\Server $server, int $taskId, $data);
}
