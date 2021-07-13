<?php

declare(strict_types=1);

namespace Imi\Kafka\Queue\Contract;

use Imi\Queue\Contract\IMessage;

interface IKafkaPushMessage extends IMessage
{
    public function getKey(): ?string;

    public function setKey(?string $key): void;

    public function getHeaders(): array;

    public function setHeaders(array $headers): void;

    public function getPartition(): ?int;

    public function setPartition(?int $partition): void;

    public function getBrokerId(): ?int;

    public function setBrokerId(?int $brokerId): void;
}
