<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 中间件注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Server\Http\Parser\ControllerParser")
 *
 * @property string|string[]|null $middlewares 中间件类或数组
 */
#[\Attribute]
class Middleware extends Base
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
