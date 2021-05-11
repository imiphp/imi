<?php

namespace Imi\AMQP\Queue;

use Imi\AMQP\Annotation\Consumer;
use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BaseConsumer;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Message;
use Swoole\Coroutine\Channel;

class QueueConsumer extends BaseConsumer
{
    /**
     * 结果通道.
     *
     * @var \Swoole\Coroutine\Channel
     */
    private $resultChannel;

    /**
     * 本地缓存的队列长度.
     *
     * @var int
     */
    protected $queueLength;

    public function __construct(int $queueLength, array $exchanges, array $queues, array $consumers, string $poolName = null)
    {
        parent::__construct();

        $this->queueLength = $queueLength;
        $this->poolName = $poolName;

        $list = [];
        foreach ($exchanges as $exchange)
        {
            $list[] = new Exchange($exchange);
        }
        $this->exchanges = $list;

        $list = [];
        foreach ($queues as $queue)
        {
            $list[] = new Queue($queue);
        }
        $this->queues = $list;

        $list = [];
        foreach ($consumers as $consumer)
        {
            $list[] = new Consumer($consumer);
        }
        $this->consumers = $list;

        $this->resultChannel = new Channel();
        $this->reopen();
    }

    /**
     * 初始化配置.
     *
     * @return void
     */
    protected function initConfig()
    {
    }

    /**
     * 重新打开
     *
     * @return void
     */
    public function reopen()
    {
        if ($this->channel)
        {
            $this->stop();
        }
        $this->connection = $this->getConnection();
        $this->channel = $this->connection->channel();
    }

    /**
     * 弹出消息.
     *
     * @param float $timeout
     *
     * @return \Imi\AMQP\Message|null
     */
    public function pop(float $timeout): ?Message
    {
        if (!$this->channel->is_consuming())
        {
            $this->reopen();
            $this->channel->basic_qos(0, $this->queueLength, false);
            $this->declareConsumer();
            $this->bindConsumer();
        }
        try
        {
            $this->channel->wait(null, false, $timeout);
            if ($this->resultChannel->isEmpty())
            {
                return null;
            }

            return $this->resultChannel->pop(0.001) ?: null;
        }
        catch (\PhpAmqpLib\Exception\AMQPTimeoutException $te)
        {
        }

        return null;
    }

    /**
     * 绑定消费者.
     *
     * @return void
     */
    protected function bindConsumer()
    {
        foreach ($this->consumers as $consumer)
        {
            foreach ((array) $consumer->queue as $queueName)
            {
                $messageClass = $consumer->message ?? \Imi\AMQP\Message::class;
                $this->channel->basic_consume($queueName, $consumer->tag, false, false, false, false, function (\PhpAmqpLib\Message\AMQPMessage $message) use ($messageClass) {
                    /** @var \Imi\AMQP\Message $messageInstance */
                    $messageInstance = new $messageClass();
                    $messageInstance->setAMQPMessage($message);
                    $this->consume($messageInstance);
                });
            }
        }
    }

    /**
     * 消费任务
     *
     * @param \Imi\AMQP\Contract\IMessage $message
     *
     * @return mixed
     */
    protected function consume(IMessage $message)
    {
        $this->resultChannel->push($message);
    }
}
