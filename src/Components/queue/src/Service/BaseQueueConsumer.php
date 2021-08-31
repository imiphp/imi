<?php

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
use function Yurun\Swoole\Coroutine\goWait;

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
    protected $imiQueue;

    /**
     * 队列名称.
     *
     * @var string
     */
    private $name;

    /**
     * 是否正在工作.
     *
     * @var bool
     */
    private $working = false;

    /**
     * 协程工作池.
     *
     * @var \Yurun\Swoole\CoPool\CoPool|null
     */
    private $coPool;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    /**
     * 开始消费循环.
     *
     * @param int|null $co
     *
     * @return void
     */
    public function start(?int $co = null)
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
                 * @param ITaskParam $param
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
     *
     * @return void
     */
    public function stop()
    {
        $this->working = false;
        if ($this->coPool)
        {
            $this->coPool->stop();
        }
    }

    /**
     * 处理消费.
     *
     * @param \Imi\Queue\Contract\IMessage   $message
     * @param \Imi\Queue\Driver\IQueueDriver $queue
     *
     * @return void
     */
    abstract protected function consume(IMessage $message, IQueueDriver $queue);
}
