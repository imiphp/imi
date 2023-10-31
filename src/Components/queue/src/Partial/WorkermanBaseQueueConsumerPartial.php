<?php

declare(strict_types=1);

namespace Imi\Queue\Partial;

use Imi\Bean\Annotation\Partial;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Queue\Event\Param\ConsumerAfterConsumeParam;
use Imi\Queue\Event\Param\ConsumerAfterPopParam;
use Imi\Queue\Event\Param\ConsumerBeforeConsumeParam;
use Imi\Queue\Event\Param\ConsumerBeforePopParam;

if (\Imi\Util\Imi::checkAppType('workerman'))
{
    /**
     * @property \Imi\Queue\Service\QueueService $imiQueue
     */
    #[Partial(class: \Imi\Queue\Service\BaseQueueConsumer::class)]
    trait WorkermanBaseQueueConsumerPartial
    {
        /**
         * 开始消费循环.
         */
        public function start(?int $co = null): void
        {
            $this->working = true;
            $config = $this->imiQueue->getQueueConfig($this->name);
            $queue = $this->imiQueue->getQueue($this->name);
            do
            {
                try
                {
                    Event::trigger('IMI.QUEUE.CONSUMER.BEFORE_POP', [
                        'queue' => $queue,
                    ], $this, ConsumerBeforePopParam::class);
                    $message = $queue->pop();
                    Event::trigger('IMI.QUEUE.CONSUMER.AFTER_POP', [
                        'queue'     => $queue,
                        'message'   => $message,
                    ], $this, ConsumerAfterPopParam::class);
                    if (null === $message)
                    {
                        usleep((int) ($config->getTimespan() * 1000000));
                    }
                    else
                    {
                        Event::trigger('IMI.QUEUE.CONSUMER.BEFORE_CONSUME', [
                            'queue'     => $queue,
                            'message'   => $message,
                        ], $this, ConsumerBeforeConsumeParam::class);
                        $this->consume($message, $queue);
                        Event::trigger('IMI.QUEUE.CONSUMER.AFTER_CONSUME', [
                            'queue'     => $queue,
                            'message'   => $message,
                        ], $this, ConsumerAfterConsumeParam::class);
                    }
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            }
            while ($this->working);
        }

        /**
         * 停止消费.
         */
        public function stop(): void
        {
            $this->working = false;
        }
    }
}
