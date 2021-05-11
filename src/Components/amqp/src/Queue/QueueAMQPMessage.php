<?php

namespace Imi\AMQP\Queue;

use Imi\Queue\Model\Message;

class QueueAMQPMessage extends Message
{
    /**
     * AMQP 消息.
     *
     * @var \Imi\AMQP\Contract\IMessage
     */
    protected $amqpMessage;

    /**
     * Get aMQP 消息.
     *
     * @return \Imi\AMQP\Contract\IMessage
     */
    public function getAmqpMessage()
    {
        return $this->amqpMessage;
    }

    /**
     * Set aMQP 消息.
     *
     * @param \Imi\AMQP\Contract\IMessage $amqpMessage AMQP 消息
     *
     * @return self
     */
    public function setAmqpMessage(\Imi\AMQP\Contract\IMessage $amqpMessage)
    {
        $this->amqpMessage = $amqpMessage;
        $this->loadFromArray($this->getAmqpMessage()->getBodyData());

        return $this;
    }
}
