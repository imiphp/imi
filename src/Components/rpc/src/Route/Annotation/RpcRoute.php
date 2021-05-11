<?php

namespace Imi\Rpc\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Rpc\Route\Annotation\Contract\IRpcRoute;

/**
 * RPC 路由注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Rpc\Route\Annotation\Parser\RpcControllerParser")
 */
abstract class RpcRoute extends Base implements IRpcRoute
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 路由名称规则.
     *
     * @var array
     */
    public $name;

    /**
     * RPC 协议类型.
     *
     * 继承本类后必须赋值
     *
     * @var string
     */
    public $rpcType;

    /**
     * 获取 RPC 类型.
     *
     * @return string
     */
    public function getRpcType()
    {
        return $this->rpcType;
    }
}
