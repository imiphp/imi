<?php

declare(strict_types=1);

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
 *
 * @property mixed  $name    路由名称规则
 * @property string $rpcType RPC 协议类型，继承本类后必须赋值
 */
abstract class RpcRoute extends Base implements IRpcRoute
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * @param mixed $name
     */
    public function __construct(?array $__data = null, $name = null, string $rpcType = '')
    {
        parent::__construct(...\func_get_args());
    }

    /**
     * 获取 RPC 类型.
     */
    public function getRpcType(): string
    {
        return $this->rpcType;
    }
}
