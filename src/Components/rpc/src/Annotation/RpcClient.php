<?php

declare(strict_types=1);

namespace Imi\Rpc\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Inherit;
use Imi\Rpc\Client\Pool\RpcClientPool;

/**
 * RPC 客户端注入.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class RpcClient extends Inject
{
    public function __construct(
        public string $name = '',
        public array $args = [],
        /**
         * 连接池名称.
         */
        public ?string $poolName = null
    ) {
    }

    /**
     * 获取注入值的真实值
     */
    public function getRealValue(): mixed
    {
        return RpcClientPool::getInstance($this->poolName);
    }
}
