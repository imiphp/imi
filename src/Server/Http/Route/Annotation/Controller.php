<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 控制器注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Server\Http\Parser\ControllerParser")
 */
#[\Attribute]
class Controller extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'prefix';

    /**
     * 路由前缀
     */
    public ?string $prefix = null;

    public function __construct(?array $__data = null, ?string $prefix = null)
    {
        parent::__construct(...\func_get_args());
    }
}
