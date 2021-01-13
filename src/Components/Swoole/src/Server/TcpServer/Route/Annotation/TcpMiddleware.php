<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 中间件注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Swoole\Server\TcpServer\Parser\TcpControllerParser")
 */
class TcpMiddleware extends Base
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
