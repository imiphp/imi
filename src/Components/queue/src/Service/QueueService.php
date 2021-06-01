<?php

declare(strict_types=1);

namespace Imi\Queue\Service;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Exception\QueueException;
use Imi\Queue\Model\QueueConfig;

/**
 * 队列的服务层类.
 *
 * @Bean("imiQueue")
 */
class QueueService
{
    /**
     * 队列列表.
     *
     * @var array
     */
    protected $list;

    /**
     * 默认队列.
     *
     * @var string|null
     */
    protected $default;

    /**
     * 队列对象集合.
     *
     * @var \Imi\Queue\Driver\IQueueDriver[]
     */
    private $queueInstances;

    /**
     * 配置集合.
     *
     * @var \Imi\Queue\Model\QueueConfig[]
     */
    private $configs = [];

    /**
     * Get 队列列表.
     *
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Set 队列列表.
     *
     * @param array $list 队列列表
     *
     * @return self
     */
    public function setList(array $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * 获取队列配置.
     */
    public function getQueueConfig(?string $name = null): QueueConfig
    {
        if (null === $name)
        {
            $name = $this->default;
            if (null === $name)
            {
                throw new QueueException('The queue name is null, and you have not configured the default parameter');
            }
        }
        $configs = &$this->configs;
        if (isset($configs[$name]))
        {
            return $configs[$name];
        }
        $list = &$this->list;
        if (!isset($list[$name]))
        {
            throw new QueueException(sprintf('Queue %s not found', $name));
        }

        return $configs[$name] = new QueueConfig($name, $list[$name]);
    }

    /**
     * 获取队列对象
     */
    public function getQueue(?string $name = null): IQueueDriver
    {
        $config = $this->getQueueConfig($name);
        if (null === $name)
        {
            $name = $config->getName();
        }
        $queueInstances = &$this->queueInstances;
        if (isset($queueInstances[$name]))
        {
            return $queueInstances[$name];
        }

        return $queueInstances[$name] = App::getBean($config->getDriver(), $name, $config->getConfig());
    }
}
