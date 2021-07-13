<?php

declare(strict_types=1);

namespace Imi\Queue\Service;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Event\Event;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Event\Param\ConsumerAfterConsumeParam;
use Imi\Queue\Event\Param\ConsumerAfterPopParam;
use Imi\Queue\Event\Param\ConsumerBeforeConsumeParam;
use Imi\Queue\Event\Param\ConsumerBeforePopParam;
use Swoole\Coroutine;
use Yurun\Swoole\CoPool\CoPool;
use Yurun\Swoole\CoPool\Interfaces\ICoTask;
use Yurun\Swoole\CoPool\Interfaces\ITaskParam;

/**
 * 队列消费基类.
 */
abstract class BaseQueueConsumer
{
    /**
     * @Inject("imiQueue")
     *
     * @var \Imi\Queue\Service\QueueService
     */
    protected QueueService $imiQueue;

    /**
     * 队列名称.
     */
    private string $name;

    /**
     * 是否正在工作.
     */
    private bool $working = false;

    /**
     * 协程工作池.
     */
    private ?CoPool $coPool;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

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
        $task = function () use ($config) {
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
                        Coroutine::sleep($config->getTimespan());
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
            } while ($this->working);
        };
        if ($co > 0)
        {
            // @phpstan-ignore-next-line
            $this->coPool = $pool = new CoPool($co, $co, new class() implements ICoTask {
                /**
                 * 执行任务
                 *
                 * @return mixed
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
                    'task'  => $task,
                ]);
            }
            $pool->wait();
        }
        else
        {
            ($task)();
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

    /**
     * 处理消费.
     */
    abstract protected function consume(IMessage $message, IQueueDriver $queue): void;
}
