<?php

declare(strict_types=1);

namespace Imi\Queue\Model;

class QueueConfig
{
    /**
     * 队列名称.
     */
    private string $name = '';

    /**
     * 使用的队列驱动.
     */
    private string $driver = 'RedisQueueDriver';

    /**
     * 消费协程数量.
     */
    private int $co = 1;

    /**
     * 消费进程数量.
     *
     * 可能会受进程分组影响，以同一组中配置的最多进程数量为准
     */
    private int $process = 1;

    /**
     * 消费循环尝试 pop 的时间间隔，单位：秒.
     */
    private float $timespan = 0.03;

    /**
     * 驱动类所需要的参数数组.
     */
    private array $config = [];

    /**
     * 进程分组名称.
     */
    private string $processGroup = 'default';

    /**
     * 自动消费.
     */
    private bool $autoConsumer = true;

    /**
     * 消费者类.
     */
    private string $consumer = '';

    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        if (isset($data['driver']))
        {
            $this->driver = $data['driver'];
        }
        if (isset($data['co']))
        {
            $this->co = $data['co'];
        }
        if (isset($data['process']))
        {
            $this->process = $data['process'];
        }
        if (isset($data['timespan']))
        {
            $this->timespan = $data['timespan'];
        }
        if (isset($data['config']))
        {
            $this->config = $data['config'];
        }
        if (isset($data['processGroup']))
        {
            $this->processGroup = $data['processGroup'];
        }
        if (isset($data['autoConsumer']))
        {
            $this->autoConsumer = $data['autoConsumer'];
        }
        if (isset($data['consumer']))
        {
            $this->consumer = $data['consumer'];
        }
    }

    /**
     * Get 使用的队列驱动.
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Get 消费协程数量.
     */
    public function getCo(): int
    {
        return $this->co;
    }

    /**
     * Get 消费进程数量.
     */
    public function getProcess(): int
    {
        return $this->process;
    }

    /**
     * Get 消费循环尝试 pop 的时间间隔，单位：秒.
     */
    public function getTimespan(): float
    {
        return $this->timespan;
    }

    /**
     * Get 驱动类所需要的参数数组.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get 队列名称.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get 进程分组名称.
     */
    public function getProcessGroup(): string
    {
        return $this->processGroup;
    }

    /**
     * Get 自动消费.
     */
    public function getAutoConsumer(): bool
    {
        return $this->autoConsumer;
    }

    /**
     * Get 消费者类.
     */
    public function getConsumer(): string
    {
        return $this->consumer;
    }
}
