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
 */
#[\Attribute]
class UdpController extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'prefix';

    public function __construct(?array $__data = null)
    {
        parent::__construct(...\func_get_args());
    }
}
