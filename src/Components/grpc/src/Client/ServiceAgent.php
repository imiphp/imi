<?php

declare(strict_types=1);

namespace Imi\Grpc\Client;

use Imi\Rpc\Client\Pool\RpcClientPool;

/**
 * 服务代理类.
 */
class ServiceAgent
{
    /**
     * 连接池名称.
     */
    public ?string $poolName = null;

    /**
     * 服务名称.
     */
    public string $serviceName = '';

    /**
     * 服务接口.
     */
    public string $interface = '';

    public function __construct(?string $poolName, string $serviceName, string $interface)
    {
        $this->poolName = $poolName;
        $this->serviceName = $serviceName;
        $this->interface = $interface;
    }

    /**
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        /** @var GrpcClient $client */
        $client = RpcClientPool::getInstance($this->poolName);
        $service = $client->getService($this->serviceName, $this->interface);

        return $service->$name(...$arguments);
    }
}
