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
     */
    protected array $list;

    /**
     * 默认队列.
     */
    protected ?string $default = null;

    /**
     * 队列对象集合.
     *
     * @var \Imi\Queue\Driver\IQueueDriver[]
     */
    private array $queueInstances = [];

    /**
     * 配置集合.
     *
     * @var \Imi\Queue\Model\QueueConfig[]
     */
    private array $configs = [];

    /**
     * Get 队列列表.
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * Set 队列列表.
     *
     * @param array $list 队列列表
     */
    public function setList(array $list): self
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

        if (isset($this->configs[$name]))
        {
            return $this->configs[$name];
        }

        if (!isset($this->list[$name]))
        {
            throw new QueueException(sprintf('Queue %s not found', $name));
        }

        return $this->configs[$name] = new QueueConfig($name, $this->list[$name]);
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

        if (isset($this->queueInstances[$name]))
        {
            return $this->queueInstances[$name];
        }

        return $this->queueInstances[$name] = App::getBean($config->getDriver(), $name, $config->getConfig());
    }
}
