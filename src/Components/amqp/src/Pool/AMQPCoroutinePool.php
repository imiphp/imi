<?php

declare(strict_types=1);

namespace Imi\AMQP\Pool;

use Imi\AMQP\Swoole\AMQPSwooleConnection;
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

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
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
        if (isset($config['heartbeat']))
        {
            $heartbeat = (int) $config['heartbeat'];
        }
        elseif (($poolHeartbeatInterval = $this->getConfig()->getHeartbeatInterval()) > 0)
        {
            $heartbeat = (int) ($poolHeartbeatInterval * 2);
        }
        else
        {
            $heartbeat = 0;
        }
        $class = $config['connectionClass'] ?? AMQPSwooleConnection::class;

        return BeanFactory::newInstance(AMQPResource::class, $this, new $class($config['host'], (int) $config['port'], $config['user'], $config['password'], $config['vhost'] ?? '/', (bool) ($config['insist'] ?? false), $config['loginMethod'] ?? 'AMQPLAIN', $config['loginResponse'] ?? null, $config['locale'] ?? 'en_US', (float) ($config['connectionTimeout'] ?? 3.0), (float) ($config['readWriteTimeout'] ?? 3.0), $config['context'] ?? null, (bool) ($config['keepalive'] ?? false), $heartbeat, (float) ($config['channelRpcTimeout'] ?? 0.0)));
    }
}
