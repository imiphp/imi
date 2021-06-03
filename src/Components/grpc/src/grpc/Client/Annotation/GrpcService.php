<?php

declare(strict_types=1);

namespace Imi\Grpc\Client\Annotation;

use Imi\Bean\Annotation\Inherit;
use Imi\Bean\BeanFactory;
use Imi\Rpc\Annotation\RpcService;

/**
 * gRPC 服务对象注入.
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class GrpcService extends RpcService
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
     * 服务接口.
     *
     * @var string
     */
    public $interface;

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
