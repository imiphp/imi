<?php

declare(strict_types=1);

namespace Imi\AMQP\Queue;

use Imi\AMQP\Contract\IMessage;
use Imi\Queue\Model\Message;

class QueueAMQPMessage extends Message
{
    /**
     * AMQP 消息.
     */
    protected IMessage $amqpMessage;

    /**
     * Get aMQP 消息.
     */
    public function getAmqpMessage(): IMessage
    {
        return $this->amqpMessage;
    }

    /**
     * Set aMQP 消息.
     *
     * @param \Imi\AMQP\Contract\IMessage $amqpMessage AMQP 消息
     */
    public function setAmqpMessage(IMessage $amqpMessage): self
    {
        $this->amqpMessage = $amqpMessage;
        $this->loadFromArray($this->getAmqpMessage()->getBodyData());

        return $this;
    }
}
