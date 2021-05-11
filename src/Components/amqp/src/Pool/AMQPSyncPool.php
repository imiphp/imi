<?php

namespace Imi\AMQP\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\BaseSyncPool;
use Imi\Pool\TUriResourceConfig;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * 同步 AMQP 客户端连接池.
 */
class AMQPSyncPool extends BaseSyncPool
{
    use TUriResourceConfig;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * 创建资源.
     *
     * @return \Imi\Pool\Interfaces\IPoolResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost'] ?? '/', $config['insist'] ?? false, $config['loginMethod'] ?? 'AMQPLAIN', $config['loginResponse'] ?? null, $config['locale'] ?? 'en_US', $config['connectionTimeout'] ?? 3.0, $config['readWriteTimeout'] ?? 3.0, $config['context'] ?? null, $config['keepalive'] ?? false, $config['heartbeat'] ?? 0, $config['channelRpcTimeout'] = 0.0, $config['sslProtocol'] ?? null);

        return BeanFactory::newInstance(AMQPResource::class, $this, $connection);
    }
}
