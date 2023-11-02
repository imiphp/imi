<?php

declare(strict_types=1);

namespace Imi\Rpc\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Rpc\Route\Annotation\Contract\IRpcRoute;

/**
 * RPC 路由注解.
 */
#[Parser(className: \Imi\Rpc\Route\Annotation\Parser\RpcControllerParser::class)]
abstract class RpcRoute extends Base implements IRpcRoute
{
    public function __construct(
        /**
         * 路由名称规则.
         *
         * @var mixed
         */
        public $name = null,
        /**
         * RPC 协议类型，继承本类后必须赋值
         */
        public string $rpcType = ''
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getRpcType(): string
    {
        return $this->rpcType;
    }
}
