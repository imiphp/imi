<?php

declare(strict_types=1);

namespace Imi\Kafka\Queue\Model;

use Imi\Kafka\Queue\Contract\IKafkaPushMessage;
use Imi\Queue\Model\Message;

class KafkaPushMessage extends Message implements IKafkaPushMessage
{
    protected ?string $key = null;

    protected array $headers = [];

    protected ?int $partition = null;

    protected ?int $brokerId = null;

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getPartition(): ?int
    {
        return $this->partition;
    }

    public function setPartition(?int $partition): void
    {
        $this->partition = $partition;
    }

    public function getBrokerId(): ?int
    {
        return $this->brokerId;
    }

    public function setBrokerId(?int $brokerId): void
    {
        $this->brokerId = $brokerId;
    }
}
