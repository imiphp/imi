<?php

declare(strict_types=1);

namespace Imi\Rpc\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Inherit;
use Imi\Rpc\Client\Pool\RpcClientPool;

/**
 * RPC 服务对象注入.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class RpcService extends Inject
{
    public function __construct(
        public string $name = '',
        public array $args = [],
        /**
         * 连接池名称.
         */
        public ?string $poolName = null,
        /**
         * 服务名称.
         */
        public ?string $serviceName = null
    ) {
    }

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return RpcClientPool::getService($this->serviceName, $this->poolName);
    }
}
