<?php

namespace Imi\Kafka\Queue\Model;

use Imi\Kafka\Queue\Contract\IKafkaPopMessage;
use Imi\Queue\Model\Message;
use longlang\phpkafka\Consumer\ConsumeMessage;

class KafkaPopMessage extends Message implements IKafkaPopMessage
{
    /**
     * @var ConsumeMessage
     */
    protected $consumeMessage;

    public function getConsumeMessage(): ConsumeMessage
    {
        return $this->consumeMessage;
    }

    /**
     * @return void
     */
    public function setConsumeMessage(ConsumeMessage $consumeMessage)
    {
        $this->consumeMessage = $consumeMessage;
    }
}
