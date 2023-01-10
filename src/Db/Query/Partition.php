<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IPartition;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Traits\TRaw;

class Partition implements IPartition
{
    use TRaw;

    protected ?array $partitions = null;

    public function setPartitions(?array $partitions): void
    {
        $this->partitions = $partitions;
    }

    public function getPartitions(): ?array
    {
        return $this->partitions;
    }

    public function toString(IQuery $query): string
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }
        elseif ($this->partitions)
        {
            return implode(',', array_map(static fn (string $name) => $query->fieldQuote($name), $this->partitions));
        }

        return '';
    }

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array
    {
        return [];
    }
}
