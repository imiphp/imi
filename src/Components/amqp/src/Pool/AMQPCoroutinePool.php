<?php

declare(strict_types=1);

namespace Imi\AMQP\Pool;

use Imi\AMQP\Swoole\AMQPSwooleConnection;
use Imi\Bean\BeanFactory;
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
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        if (isset($config['heartbeat']))
        {
            $heartbeat = $config['heartbeat'];
        }
        elseif (($poolHeartbeatInterval = $this->getConfig()->getHeartbeatInterval()) > 0)
        {
            $heartbeat = $poolHeartbeatInterval * 2;
        }
        else
        {
            $heartbeat = 0;
        }

        return BeanFactory::newInstance(AMQPResource::class, $this, new ($config['connectionClass'] ?? AMQPSwooleConnection::class)($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost'] ?? '/', $config['insist'] ?? false, $config['loginMethod'] ?? 'AMQPLAIN', $config['loginResponse'] ?? null, $config['locale'] ?? 'en_US', $config['connectionTimeout'] ?? 3.0, $config['readWriteTimeout'] ?? 3.0, $config['context'] ?? null, $config['keepalive'] ?? false, $heartbeat, $config['channelRpcTimeout'] ?? 0.0));
    }
}
