<?php

declare(strict_types=1);

namespace Imi\Kafka\Queue\Model;

use Imi\Kafka\Queue\Contract\IKafkaPopMessage;
use Imi\Queue\Model\Message;
use longlang\phpkafka\Consumer\ConsumeMessage;

class KafkaPopMessage extends Message implements IKafkaPopMessage
{
    protected ?ConsumeMessage $consumeMessage = null;

    public function getConsumeMessage(): ConsumeMessage
    {
        return $this->consumeMessage;
    }

    public function setConsumeMessage(ConsumeMessage $consumeMessage): void
    {
        $this->consumeMessage = $consumeMessage;
    }
}
