<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 控制器注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Server\UdpServer\Parser\UdpControllerParser")
 *
 * @property string|string[]|null $server 指定当前控制器允许哪些服务器使用；支持字符串或数组，默认为 null 则不限制
 */
#[\Attribute]
class UdpController extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'prefix';

    /**
     * @param string|string[]|null $server
     */
    public function __construct(?array $__data = null, $server = null)
    {
        parent::__construct(...\func_get_args());
    }
}
