<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 中间件注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Server\UdpServer\Parser\UdpControllerParser")
 *
 * @property string|string[]|null $middlewares 中间件类或数组
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class UdpMiddleware extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'middlewares';

    /**
     * @param string|string[]|null $middlewares
     */
    public function __construct(?array $__data = null, $middlewares = null)
    {
        parent::__construct(...\func_get_args());
    }
}
