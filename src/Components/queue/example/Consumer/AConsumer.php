<?php

declare(strict_types=1);

namespace QueueApp\Consumer;

use Imi\Bean\Annotation\Bean;
use Imi\Log\Log;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Service\BaseQueueConsumer;

/**
 * @Bean("AConsumer")
 */
class AConsumer extends BaseQueueConsumer
{
    /**
     * 处理消费.
     */
    protected function consume(IMessage $message, IQueueDriver $queue): void
    {
        Log::info(sprintf('[%s]%s:%s', $queue->getName(), $message->getMessageId(), $message->getMessage()));
        $queue->success($message);
    }
}
