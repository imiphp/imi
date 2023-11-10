<?php

declare(strict_types=1);

namespace Imi\AMQP\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\TUriResourceConfig;
use Imi\Swoole\Pool\BaseAsyncPool;

/**
 * 协程 AMQP 客户端连接池.
 */
class AMQPCoroutinePool extends BaseAsyncPool
{
    use TUriResourceConfig;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, mixed $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * {@inheritDoc}
     */
    protected function createResource(): IPoolResource
    {
        return $this->createNewResource();
    }

    /**
     * {@inheritDoc}
     */
    public function createNewResource(): IPoolResource
    {
        $config = $this->getNextResourceConfig();
        if (!isset($config['heartbeat']) && ($poolHeartbeatInterval = $this->getConfig()->getHeartbeatInterval()) > 0)
        {
            $config['heartbeat'] = (int) ($poolHeartbeatInterval * 2);
        }

        return BeanFactory::newInstance(AMQPResource::class, $this, AMQPPool::createInstanceFromConfig($config));
    }
}
