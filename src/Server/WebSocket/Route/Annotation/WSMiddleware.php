<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 中间件注解.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @Parser("Imi\Server\WebSocket\Parser\WSControllerParser")
 *
 * @property string|string[] $middlewares 中间件类或数组
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class WSMiddleware extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'middlewares';

    /**
     * @param string|string[] $middlewares
     */
    public function __construct(?array $__data = null, $middlewares = null)
    {
        parent::__construct(...\func_get_args());
    }
}
