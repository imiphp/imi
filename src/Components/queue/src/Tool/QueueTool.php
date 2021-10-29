<?php

declare(strict_types=1);

namespace Imi\Queue\Tool;

use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Contract\BaseCommand;
use Imi\Queue\Facade\Queue;

/**
 * @Command("queue")
 */
class QueueTool extends BaseCommand
{
    /**
     * 获取队列状态
     *
     * @CommandAction(name="status", description="获取队列状态")
     * @Argument(name="queue", type="string", required=true)
     *
     * @return void
     */
    public function status(string $queue)
    {
        fwrite(\STDOUT, json_encode(Queue::getQueue($queue)->status(), \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR) . \PHP_EOL);
    }

    /**
     * 将失败消息恢复到队列.
     *
     * @CommandAction(name="restoreFail", description="将失败消息恢复到队列")
     * @Argument(name="queue", type="string", required=true)
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
     * @CommandAction(name="restoreTimeout", description="将超时消息恢复到队列")
     * @Argument(name="queue", type="string", required=true)
     *
     * @return void
     */
    public function restoreTimeout(string $queue)
    {
        fwrite(\STDOUT, Queue::getQueue($queue)->restoreTimeoutMessages() . \PHP_EOL);
    }
}
