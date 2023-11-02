<?php

declare(strict_types=1);

namespace Imi\Grpc\Client\Annotation;

use Imi\Bean\Annotation\Inherit;
use Imi\Bean\BeanFactory;
use Imi\Rpc\Annotation\RpcService;

/**
 * gRPC 服务对象注入.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class GrpcService extends RpcService
{
    public function __construct(
        public string $name = '',
        public array $args = [],
        public ?string $poolName = null,
        public ?string $serviceName = null,
        /**
         * 服务接口.
         */
        public ?string $interface = null
    ) {
    }

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return BeanFactory::newInstance(\Imi\Grpc\Client\ServiceAgent::class, $this->poolName, $this->serviceName, $this->interface);
    }
}
