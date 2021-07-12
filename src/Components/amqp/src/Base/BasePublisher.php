<?php

declare(strict_types=1);

namespace Imi\AMQP\Base;

use Imi\AMQP\Base\Traits\TAMQP;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Contract\IPublisher;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * 发布者基类.
 */
abstract class BasePublisher implements IPublisher
{
    use TAMQP;

    /**
     * ack 是否成功
     */
    private bool $ackSuccess;

    public function __construct()
    {
        $this->initConfig();
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        if ($this->channel)
        {
            $this->channel->close();
            $this->channel = null;
        }
        $this->connection = null;
    }

    /**
     * 准备发布.
     */
    protected function preparePublish(bool $force = false): void
    {
        if (!$this->connection)
        {
            $this->connection = $this->getConnection();
        }
        if ($force || !$this->connection->isConnected())
        {
            $this->connection->reconnect();
            $this->channel = null;
        }
        if (!$this->channel)
        {
            $this->channel = $this->connection->channel();
            $this->channel->confirm_select();
            $this->declarePublisher();
        }
        $this->ackSuccess = false;
        $this->channel->set_ack_handler(function () {
            $this->ackSuccess = true;
        });
    }

    /**
     * 发布消息.
     */
    public function publish(IMessage $message): bool
    {
        $this->preparePublish();
        $amqpMessage = new AMQPMessage($message->getBody(), $message->getProperties());
        $first = true;
        $continue = true;
        foreach ($this->exchanges as $exchange)
        {
            do
            {
                try
                {
                    $this->channel->basic_publish($amqpMessage, $exchange->name, $message->getRoutingKey(), $message->getMandatory(), $message->getImmediate(), $message->getTicket());
                    $this->channel->wait_for_pending_acks_returns(3);
                    if (!$this->ackSuccess)
                    {
                        break 2;
                    }
                    $first = false;
                    $continue = false;
                }
                catch (\Throwable $th)
                {
                    if ($first)
                    {
                        $first = false;
                        $this->preparePublish(true);
                        continue;
                    }
                    else
                    {
                        throw $th;
                    }
                }
            } while ($continue);
        }
        $this->channel->set_ack_handler(function () {
        });

        return $this->ackSuccess;
    }
}
