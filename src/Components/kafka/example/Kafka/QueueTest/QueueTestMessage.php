<?php

declare(strict_types=1);

namespace KafkaApp\Kafka\QueueTest;

use Imi\Util\Traits\TNotRequiredDataToProperty;

class QueueTestMessage
{
    use TNotRequiredDataToProperty;

    /**
     * 用户ID.
     */
    protected int $memberId;

    /**
     * Get 用户ID.
     */
    public function getMemberId(): int
    {
        return $this->memberId;
    }

    /**
     * Set 用户ID.
     *
     * @param int $memberId 用户ID
     */
    public function setMemberId(int $memberId): self
    {
        $this->memberId = $memberId;

        return $this;
    }

    public function toMessage(): string
    {
        return json_encode([
            'memberId' => $this->memberId,
        ], \JSON_THROW_ON_ERROR);
    }

    public static function fromMessage(string $message): self
    {
        return new self(json_decode($message, true));
    }
}
