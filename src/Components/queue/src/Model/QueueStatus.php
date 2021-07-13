<?php

declare(strict_types=1);

namespace Imi\Queue\Model;

use Imi\Util\Traits\TDataToProperty;
use JsonSerializable;

/**
 * 队列状态
 */
class QueueStatus implements JsonSerializable
{
    use TDataToProperty;

    /**
     * 准备就绪数量.
     */
    protected int $ready;

    /**
     * 工作中数量.
     */
    protected int $working;

    /**
     * 失败数量.
     */
    protected int $fail;

    /**
     * 超时数量.
     */
    protected int $timeout;

    /**
     * 延时数量.
     */
    protected int $delay;

    /**
     * Get 准备就绪数量.
     */
    public function getReady(): int
    {
        return $this->ready;
    }

    /**
     * Get 工作中数量.
     */
    public function getWorking(): int
    {
        return $this->working;
    }

    /**
     * Get 失败数量.
     */
    public function getFail(): int
    {
        return $this->fail;
    }

    /**
     * Get 超时数量.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get 延时数量.
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

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
