<?php

declare(strict_types=1);

namespace Imi\Rpc\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Inherit;
use Imi\Rpc\Client\Pool\RpcClientPool;

/**
 * RPC 客户端注入.
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property string|null $poolName 连接池名称
 */
#[\Attribute]
class RpcClient extends Inject
{
    public function __construct(?array $__data = null, string $name = '', array $args = [], ?string $poolName = null)
    {
        parent::__construct(...\func_get_args());
    }

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return RpcClientPool::getInstance($this->poolName);
    }
}
