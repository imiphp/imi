<?php

declare(strict_types=1);

namespace Imi\AMQP\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\BaseSyncPool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\TUriResourceConfig;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * 同步 AMQP 客户端连接池.
 */
class AMQPSyncPool extends BaseSyncPool
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
    public function createNewResource(): IPoolResource
    {
        $config = $this->getNextResourceConfig();
        $connection = new AMQPStreamConnection($config['host'], (int) $config['port'], $config['user'], $config['password'], $config['vhost'] ?? '/', (bool) ($config['insist'] ?? false), $config['loginMethod'] ?? 'AMQPLAIN', $config['loginResponse'] ?? null, $config['locale'] ?? 'en_US', (float) ($config['connectionTimeout'] ?? 3.0), (float) ($config['readWriteTimeout'] ?? 3.0), $config['context'] ?? null, (bool) ($config['keepalive'] ?? false), (int) ($config['heartbeat'] ?? 0), (float) ($config['channelRpcTimeout'] = 0.0), $config['sslProtocol'] ?? null);

        return BeanFactory::newInstance(AMQPResource::class, $this, $connection);
    }
}
