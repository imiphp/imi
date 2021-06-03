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
 */
class RpcClient extends Inject
{
    /**
     * 连接池名称.
     *
     * @var string|null
     */
    public $poolName;

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
