<?php

declare(strict_types=1);

namespace Imi\AMQP\Base;

use Imi\AMQP\Base\Traits\TAMQP;
use Imi\AMQP\Contract\IConsumer;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Enum\ConsumerResult;
use Imi\App;
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
            $this->channel->wait(null, true);
            if ($messageCount == $this->messageCount)
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
                $this->channel->basic_consume($queueName, $consumer->tag, false, false, false, false, function (\PhpAmqpLib\Message\AMQPMessage $message) use ($messageClass, $isSwoole) {
                    if (!$this->running)
                    {
                        return;
                    }
                    ++$this->messageCount;
                    $result = ConsumerResult::NACK;
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
                                    // @phpstan-ignore-next-line
                                    App::getBean('ErrorLog')->onException($th);

                                    return ConsumerResult::NACK;
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
                        // @phpstan-ignore-next-line
                        App::getBean('ErrorLog')->onException($th);
                    }
                    finally
                    {
                        switch ($result)
                        {
                            case ConsumerResult::ACK:
                                $this->channel->basic_ack($message->getDeliveryTag());
                                break;
                            case ConsumerResult::NACK:
                                $this->channel->basic_nack($message->getDeliveryTag());
                                break;
                            case ConsumerResult::NACK_REQUEUE:
                                $this->channel->basic_nack($message->getDeliveryTag(), false, true);
                                break;
                            case ConsumerResult::REJECT:
                                $this->channel->basic_reject($message->getDeliveryTag(), false);
                                break;
                            case ConsumerResult::REJECT_REQUEUE:
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
     *
     * @return mixed
     */
    abstract protected function consume(IMessage $message);
}
