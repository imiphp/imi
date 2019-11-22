<?php
namespace Imi\Cron\Message;

use Imi\App;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Worker;

class Result implements IMessage
{
    /**
     * 动作
     *
     * @var string
     */
    public $action;

    /**
     * 定时任务ID
     *
     * @var string
     */
    public $id;

    /**
     * 进程类型
     *
     * @var string
     */
    public $processType;

    /**
     * 进程标识符
     *
     * @var string
     */
    public $processId;

    /**
     * 是否成功
     *
     * @var bool
     */
    public $success;

    /**
     * 消息
     *
     * @var string
     */
    public $message;

    public function __construct($action, $id, $success, $message)
    {
        $this->action = $action;
        $this->id = $id;
        $this->success = $success;
        $this->message = $message;
        $this->processType = App::get(ProcessAppContexts::PROCESS_TYPE);
        switch($this->processType)
        {
            case ProcessType::WORKER:
            case ProcessType::TASK_WORKER:
                $this->processId = Worker::getWorkerID();
                break;
            case ProcessType::PROCESS:
                $this->processId = App::get(ProcessAppContexts::PROCESS_NAME) . '#' . Worker::getWorkerID();
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid process type %s', $this->processType));
        }
    }
}