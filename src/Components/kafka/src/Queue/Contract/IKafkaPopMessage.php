<?php

namespace Imi\Kafka\Queue\Contract;

use Imi\Queue\Contract\IMessage;
use longlang\phpkafka\Consumer\ConsumeMessage;

interface IKafkaPopMessage extends IMessage
{
    public function getConsumeMessage(): ConsumeMessage;

    /**
     * @return void
     */
    public function setConsumeMessage(ConsumeMessage $consumeMessage);
}
