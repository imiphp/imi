<?php

declare(strict_types=1);

namespace Imi\AMQP\Base;

use Imi\AMQP\Base\Traits\TAMQP;
use Imi\AMQP\Contract\IConsumer;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Enum\ConsumerResult;
use Imi\Log\Log;
use Imi\Util\Imi;

use function Yurun\Swoole\Coroutine\goWait;

/**
 * 消费者基类.
 */
abstract class BaseConsumer implements IConsumer
{
    use TAMQP;

    protected bool $running = false;

    protected int $messageCount = 0;

    public function __construct()
    {
        $this->initConfig();
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
        $this->running = true;
        $this->connection = $this->getConnection();
        $this->channel = $this->connection->channel();
        $this->declareConsumer();
        $this->bindConsumer();
        $messageCount = $this->messageCount;
        while ($this->running && $this->channel && $this->channel->is_consuming())
        {
            $this->channel->getConnection()->checkHeartBeat();
            $this->channel->wait(null, true);
            if ($messageCount === $this->messageCount)
            {
                usleep(10000);
            }
            else
            {
                $messageCount = $this->messageCount;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function stop(): void
    {
        $this->running = false;

        if ($this->channel)
        {
            $this->channel->close();
            $this->channel = null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->stop();
        $this->connection = null;
    }

    /**
     * 绑定消费者.
     */
    protected function bindConsumer(): void
    {
        $isSwoole = Imi::checkAppType('swoole');
        foreach ($this->consumers as $consumer)
        {
            foreach ((array) $consumer->queue as $queueName)
            {
                $messageClass = $consumer->message ?? \Imi\AMQP\Message::class;
                $this->channel->basic_consume($queueName, $consumer->tag, false, false, false, false, function (\PhpAmqpLib\Message\AMQPMessage $message) use ($messageClass, $isSwoole): void {
                    if (!$this->running)
                    {
                        return;
                    }
                    ++$this->messageCount;
                    $result = ConsumerResult::Nack;
                    try
                    {
                        /** @var \Imi\AMQP\Message $messageInstance */
                        $messageInstance = new $messageClass();
                        $messageInstance->setAMQPMessage($message);
                        if ($isSwoole)
                        {
                            $result = goWait(function () use ($messageInstance) {
                                try
                                {
                                    return $this->consume($messageInstance);
                                }
                                catch (\Throwable $th)
                                {
                                    Log::error($th);

                                    return ConsumerResult::Nack;
                                }
                            });
                        }
                        else
                        {
                            $result = $this->consume($messageInstance);
                        }
                    }
                    catch (\Throwable $th)
                    {
                        Log::error($th);
                    }
                    finally
                    {
                        switch ($result)
                        {
                            case ConsumerResult::Ack:
                                $this->channel->basic_ack($message->getDeliveryTag());
                                break;
                            case ConsumerResult::Nack:
                                $this->channel->basic_nack($message->getDeliveryTag());
                                break;
                            case ConsumerResult::NackRequeue:
                                $this->channel->basic_nack($message->getDeliveryTag(), false, true);
                                break;
                            case ConsumerResult::Reject:
                                $this->channel->basic_reject($message->getDeliveryTag(), false);
                                break;
                            case ConsumerResult::RejectRequeue:
                                $this->channel->basic_reject($message->getDeliveryTag(), true);
                                break;
                        }
                    }
                });
            }
        }
    }

    /**
     * 消费任务
     */
    abstract protected function consume(IMessage $message): ConsumerResult;
}
