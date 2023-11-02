<?php

declare(strict_types=1);

namespace Imi\Rpc\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Rpc\Route\Annotation\Contract\IRpcController;

/**
 * RPC 控制器注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
#[Parser(className: \Imi\Rpc\Route\Annotation\Parser\RpcControllerParser::class)]
class RpcController extends Base implements IRpcController
{
    public function __construct(
        /**
         * 路由前缀
         */
        public string $prefix = '',
        /**
         * 指定当前控制器允许哪些服务器使用。支持字符串或数组，默认为 null 则不限制。
         *
         * @var string|string[]|null
         */
        public $server = null
    ) {
    }
}
