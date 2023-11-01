<?php

declare(strict_types=1);

namespace Imi\Queue\Tool;

use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Contract\BaseCommand;
use Imi\Queue\Facade\Queue;

#[Command(name: 'queue')]
class QueueTool extends BaseCommand
{
    /**
     * 获取队列状态
     */
    #[CommandAction(name: 'status', description: '获取队列状态')]
    #[Argument(name: 'queue', type: \Imi\Cli\ArgType::STRING, required: true)]
    public function status(string $queue): void
    {
        fwrite(\STDOUT, json_encode(Queue::getQueue($queue)->status(), \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE) . \PHP_EOL);
    }

    /**
     * 将失败消息恢复到队列.
     */
    #[CommandAction(name: 'restoreFail', description: '将失败消息恢复到队列')]
    #[Argument(name: 'queue', type: \Imi\Cli\ArgType::STRING, required: true)]
    public function restoreFail(string $queue): void
    {
        fwrite(\STDOUT, Queue::getQueue($queue)->restoreFailMessages() . \PHP_EOL);
    }

    /**
     * 将超时消息恢复到队列.
     */
    #[CommandAction(name: 'restoreTimeout', description: '将超时消息恢复到队列')]
    #[Argument(name: 'queue', type: \Imi\Cli\ArgType::STRING, required: true)]
    public function restoreTimeout(string $queue): void
    {
        fwrite(\STDOUT, Queue::getQueue($queue)->restoreTimeoutMessages() . \PHP_EOL);
    }
}
