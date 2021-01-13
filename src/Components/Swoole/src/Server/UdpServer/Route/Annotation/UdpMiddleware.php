<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 中间件注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Swoole\Server\UdpServer\Parser\UdpControllerParser")
 */
class UdpMiddleware extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'middlewares';

    /**
     * 中间件类或数组.
     *
     * @var string|string[]
     */
    public $middlewares;
}
