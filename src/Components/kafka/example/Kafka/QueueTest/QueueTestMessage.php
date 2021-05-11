<?php

namespace KafkaApp\Kafka\QueueTest;

use Imi\Util\Traits\TNotRequiredDataToProperty;

class QueueTestMessage
{
    use TNotRequiredDataToProperty;

    /**
     * 用户ID.
     *
     * @var int
     */
    protected $memberId;

    /**
     * Get 用户ID.
     *
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * Set 用户ID.
     *
     * @param int $memberId 用户ID
     *
     * @return self
     */
    public function setMemberId(int $memberId)
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * @return string
     */
    public function toMessage(): string
    {
        return json_encode([
            'memberId' => $this->memberId,
        ]);
    }

    public static function fromMessage(string $message): self
    {
        return new self(json_decode($message, true));
    }
}
