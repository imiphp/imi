<?php

declare(strict_types=1);

namespace Imi\Kafka\Queue\Model;

use Imi\Kafka\Queue\Contract\IKafkaPushMessage;
use Imi\Queue\Model\Message;

class KafkaPushMessage extends Message implements IKafkaPushMessage
{
    /**
     * @var string|null
     */
    protected $key = null;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var int|null
     */
    protected $partition = null;

    /**
     * @var int|null
     */
    protected $brokerId = null;

    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @return void
     */
    public function setKey(?string $key)
    {
        $this->key = $key;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getPartition(): ?int
    {
        return $this->partition;
    }

    /**
     * @return void
     */
    public function setPartition(?int $partition)
    {
        $this->partition = $partition;
    }

    public function getBrokerId(): ?int
    {
        return $this->brokerId;
    }

    /**
     * @return void
     */
    public function setBrokerId(?int $brokerId)
    {
        $this->brokerId = $brokerId;
    }
}
