<?php

declare(strict_types=1);

namespace Imi\Rpc\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Rpc\Route\Annotation\Contract\IRpcController;

/**
 * RPC 控制器注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Rpc\Route\Annotation\Parser\RpcControllerParser")
 *
 * @property string               $prefix 路由前缀
 * @property string|string[]|null $server 指定当前控制器允许哪些服务器使用。支持字符串或数组，默认为 null 则不限制。
 */
#[\Attribute]
class RpcController extends Base implements IRpcController
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'prefix';

    /**
     * @param string|string[]|null $server
     */
    public function __construct(?array $__data = null, string $prefix = '', $server = null)
    {
        parent::__construct(...\func_get_args());
    }
}
