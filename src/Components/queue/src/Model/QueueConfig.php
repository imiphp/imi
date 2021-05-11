<?php

namespace Imi\Queue\Model;

class QueueConfig
{
    /**
     * 队列名称.
     *
     * @var string
     */
    private $name;

    /**
     * 使用的队列驱动.
     *
     * @var string
     */
    private $driver = 'RedisQueueDriver';

    /**
     * 消费协程数量.
     *
     * @var int
     */
    private $co = 1;

    /**
     * 消费进程数量.
     *
     * 可能会受进程分组影响，以同一组中配置的最多进程数量为准
     *
     * @var int
     */
    private $process = 1;

    /**
     * 消费循环尝试 pop 的时间间隔，单位：秒.
     *
     * @var float
     */
    private $timespan = 0.03;

    /**
     * 驱动类所需要的参数数组.
     *
     * @var array
     */
    private $config = [];

    /**
     * 进程分组名称.
     *
     * @var string
     */
    private $processGroup = 'default';

    /**
     * 自动消费.
     *
     * @var bool
     */
    private $autoConsumer = true;

    /**
     * 消费者类.
     *
     * @var string
     */
    private $consumer;

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
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get 消费协程数量.
     *
     * @return int
     */
    public function getCo()
    {
        return $this->co;
    }

    /**
     * Get 消费进程数量.
     *
     * @return int
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Get 消费循环尝试 pop 的时间间隔，单位：秒.
     *
     * @return float
     */
    public function getTimespan()
    {
        return $this->timespan;
    }

    /**
     * Get 驱动类所需要的参数数组.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get 队列名称.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get 进程分组名称.
     *
     * @return string
     */
    public function getProcessGroup()
    {
        return $this->processGroup;
    }

    /**
     * Get 自动消费.
     *
     * @return bool
     */
    public function getAutoConsumer()
    {
        return $this->autoConsumer;
    }

    /**
     * Get 消费者类.
     *
     * @return string
     */
    public function getConsumer()
    {
        return $this->consumer;
    }
}
