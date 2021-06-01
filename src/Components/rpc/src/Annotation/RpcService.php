<?php

declare(strict_types=1);

namespace Imi\Rpc\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Parser;
use Imi\Rpc\Client\Pool\RpcClientPool;

/**
 * RPC 服务对象注入.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class RpcService extends Inject
{
    /**
     * 连接池名称.
     *
     * @var string|null
     */
    public $poolName;

    /**
     * 服务名称.
     *
     * @var string
     */
    public $serviceName;

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
