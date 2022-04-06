<?php

declare(strict_types=1);

namespace Imi\Queue\Partial;

use Imi\App;
use Imi\Bean\Annotation\Partial;
use Imi\Event\Event;
use Imi\Queue\Event\Param\ConsumerAfterConsumeParam;
use Imi\Queue\Event\Param\ConsumerAfterPopParam;
use Imi\Queue\Event\Param\ConsumerBeforeConsumeParam;
use Imi\Queue\Event\Param\ConsumerBeforePopParam;
use Imi\Queue\Model\QueueConfig;
use Swoole\Coroutine;
use function Swoole\Coroutine\defer;
use Yurun\Swoole\CoPool\CoPool;
use Yurun\Swoole\CoPool\Interfaces\ICoTask;
use Yurun\Swoole\CoPool\Interfaces\ITaskParam;
use function Yurun\Swoole\Coroutine\goWait;

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    /**
     * @Partial(\Imi\Queue\Service\BaseQueueConsumer::class)
     *
     * @property \Imi\Queue\Service\QueueService $imiQueue
     */
    trait SwooleBaseQueueConsumerPartial
    {
        /**
         * 协程工作池.
         */
        private ?CoPool $coPool = null;

        /**
         * 开始消费循环.
         */
        public function start(?int $co = null): void
        {
            $this->working = true;
            $config = $this->imiQueue->getQueueConfig($this->name);
            if (null === $co)
            {
                $co = $config->getCo();
            }
            if ($co > 0)
            {
                // @phpstan-ignore-next-line
                $this->coPool = $pool = new CoPool($co, $co, new class() implements ICoTask {
                    /**
                     * {@inheritDoc}
                     */
                    public function run(ITaskParam $param)
                    {
                        ($param->getData()['task'])();
                    }
                });
                $pool->run();
                for ($i = 0; $i < $co; ++$i)
                {
                    $pool->addTaskAsync([
                        'task'  => fn () => $this->task($config),
                    ]);
                }
                $pool->wait();
            }
            else
            {
                goWait(fn () => $this->task($config));
            }
        }

        /**
         * 停止消费.
         */
        public function stop(): void
        {
            $this->working = false;
            if ($this->coPool)
            {
                $this->coPool->stop();
            }
        }

        protected function task(QueueConfig $config): void
        {
            do
            {
                $queue = $this->imiQueue->getQueue($this->name);
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
                        Coroutine::sleep($config->getTimespan());
                    }
                    else
                    {
                        goWait(function () use ($queue, $message) {
                            Event::trigger('IMI.QUEUE.CONSUMER.BEFORE_CONSUME', [
                                'queue'     => $queue,
                                'message'   => $message,
                            ], $this, ConsumerBeforeConsumeParam::class);
                            $this->consume($message, $queue);
                            Event::trigger('IMI.QUEUE.CONSUMER.AFTER_CONSUME', [
                                'queue'     => $queue,
                                'message'   => $message,
                            ], $this, ConsumerAfterConsumeParam::class);
                        });
                    }
                }
                catch (\Throwable $th)
                {
                    App::getBean('ErrorLog')->onException($th);
                    if ($this->working)
                    {
                        defer(fn () => Coroutine::create([$this, 'task']));
                        break;
                    }
                }
            }
            while ($this->working);
        }
    }
}
