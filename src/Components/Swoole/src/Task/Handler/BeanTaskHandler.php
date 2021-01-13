<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Handler;

use Imi\Bean\BeanFactory;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

class BeanTaskHandler implements ITaskHandler
{
    /**
     * 任务类类名.
     *
     * @var string
     */
    private string $taskHandlerClass;

    public function __construct(string $taskHandlerClass)
    {
        $this->taskHandlerClass = $taskHandlerClass;
    }

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
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
    {
        /** @var ITaskHandler $taskHandler */
        $taskHandler = BeanFactory::newInstance($this->taskHandlerClass);

        return $taskHandler->handle($param, $server, $taskId, $workerId);
    }

    /**
     * 任务结束时触发.
     *
     * @param \Swoole\Server $server
     * @param int            $taskId
     * @param mixed          $data
     *
     * @return void
     */
    public function finish(\Swoole\Server $server, int $taskId, $data)
    {
        /** @var ITaskHandler $taskHandler */
        $taskHandler = BeanFactory::newInstance($this->taskHandlerClass);
        $taskHandler->finish($server, $taskId, $data);
    }
}
