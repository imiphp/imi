<?php

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
     *
     * @var int
     */
    protected $ready;

    /**
     * 工作中数量.
     *
     * @var int
     */
    protected $working;

    /**
     * 失败数量.
     *
     * @var int
     */
    protected $fail;

    /**
     * 超时数量.
     *
     * @var int
     */
    protected $timeout;

    /**
     * 延时数量.
     *
     * @var int
     */
    protected $delay;

    /**
     * Get 准备就绪数量.
     *
     * @return int
     */
    public function getReady()
    {
        return $this->ready;
    }

    /**
     * Get 工作中数量.
     *
     * @return int
     */
    public function getWorking()
    {
        return $this->working;
    }

    /**
     * Get 失败数量.
     *
     * @return int
     */
    public function getFail()
    {
        return $this->fail;
    }

    /**
     * Get 超时数量.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Get 延时数量.
     *
     * @return int
     */
    public function getDelay()
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
