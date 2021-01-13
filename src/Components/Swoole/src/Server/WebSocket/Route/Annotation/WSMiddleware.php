<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 中间件注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Swoole\Server\WebSocket\Parser\WSControllerParser")
 */
class WSMiddleware extends Base
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
