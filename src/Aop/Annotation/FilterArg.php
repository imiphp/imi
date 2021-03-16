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
 */
#[\Attribute]
class FilterArg extends Base
{
    /**
     * 参数名.
     */
    public ?string $name = null;

    /**
     * 过滤器.
     *
     * @var callable|null
     */
    public $filter = null;

    public function __construct(?array $__data = null, ?string $name = null)
    {
        parent::__construct(...\func_get_args());
    }
}
