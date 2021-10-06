<?php

declare(strict_types=1);

namespace Imi\Queue\Partial;

use Imi\App;
use Imi\Bean\Annotation\Partial;
use Imi\Event\Event;

if (\Imi\Util\Imi::checkAppType('workerman'))
{
    /**
     * @Partial(Imi\Queue\Service\BaseQueueConsumer::class)
     *
     * @property \Imi\Queue\Service\QueueService $imiQueue
     */
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
                    App::getBean('ErrorLog')->onException($th);
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
