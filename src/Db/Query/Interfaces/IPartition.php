<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IPartition extends IBase
{
    public function setPartitions(?array $partitions): void;

    public function getPartitions(): ?array;
}
