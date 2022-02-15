<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool\Model;

use Imi\Util\Traits\TNotRequiredDataToProperty;
use JsonSerializable;

class PoolInfo implements JsonSerializable
{
    use TNotRequiredDataToProperty;

    /**
     * 连接池名.
     */
    protected string $name = '';

    /**
     * 进程ID.
     */
    protected int $workerId = -1;

    /**
     * 连接数量.
     */
    protected int $count = 0;

    /**
     * 正在使用的连接数量.
     */
    protected int $used = 0;

    /**
     * 空闲的连接数量.
     */
    protected int $free = 0;

    /**
     * Get 连接池名.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get 进程ID.
     */
    public function getWorkerId(): int
    {
        return $this->workerId;
    }

    /**
     * Get 连接数量.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get 正在使用的连接数量.
     */
    public function getUsed(): int
    {
        return $this->used;
    }

    /**
     * Get 空闲的连接数量.
     */
    public function getFree(): int
    {
        return $this->free;
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        // @phpstan-ignore-next-line
        foreach ($this as $k => $v)
        {
            $data[$k] = $v;
        }

        return $data;
    }
}
