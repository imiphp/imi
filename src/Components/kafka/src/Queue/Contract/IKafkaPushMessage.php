<?php

namespace Imi\Kafka\Queue\Contract;

use Imi\Queue\Contract\IMessage;

interface IKafkaPushMessage extends IMessage
{
    public function getKey(): ?string;

    /**
     * @return void
     */
    public function setKey(?string $key);

    public function getHeaders(): array;

    /**
     * @return void
     */
    public function setHeaders(array $headers);

    public function getPartition(): ?int;

    /**
     * @return void
     */
    public function setPartition(?int $partition);

    public function getBrokerId(): ?int;

    /**
     * @return void
     */
    public function setBrokerId(?int $brokerId);
}
