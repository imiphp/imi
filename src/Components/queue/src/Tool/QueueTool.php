<?php

namespace Imi\Queue\Tool;

use Imi\Queue\Facade\Queue;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;

/**
 * @Tool("queue")
 */
class QueueTool
{
    /**
     * 获取队列状态
     *
     * @Operation("status")
     * @Arg(name="queue", type="string", required=true)
     *
     * @param string $queue
     *
     * @return void
     */
    public function status(string $queue)
    {
        fwrite(\STDOUT, json_encode(Queue::getQueue($queue)->status(), \JSON_PRETTY_PRINT) . \PHP_EOL);
    }

    /**
     * 将失败消息恢复到队列.
     *
     * @Operation("restoreFail")
     * @Arg(name="queue", type="string", required=true)
     *
     * @param string $queue
     *
     * @return void
     */
    public function restoreFail(string $queue)
    {
        fwrite(\STDOUT, Queue::getQueue($queue)->restoreFailMessages() . \PHP_EOL);
    }

    /**
     * 将超时消息恢复到队列.
     *
     * @Operation("restoreTimeout")
     * @Arg(name="queue", type="string", required=true)
     *
     * @param string $queue
     *
     * @return void
     */
    public function restoreTimeout(string $queue)
    {
        fwrite(\STDOUT, Queue::getQueue($queue)->restoreTimeoutMessages() . \PHP_EOL);
    }
}
