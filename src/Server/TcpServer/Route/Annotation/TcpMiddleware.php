<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 中间件注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Server\TcpServer\Parser\TcpControllerParser")
 *
 * @property string|string[]|null $middlewares
 */
#[\Attribute]
class TcpMiddleware extends Base
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
