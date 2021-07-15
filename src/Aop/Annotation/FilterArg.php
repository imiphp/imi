<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 过滤方法参数注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null   $name   参数名
 * @property callable|null $filter 过滤器
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class FilterArg extends Base
{
    public function __construct(?array $__data = null, ?string $name = null, ?callable $filter = null)
    {
        parent::__construct(...\func_get_args());
    }
}
