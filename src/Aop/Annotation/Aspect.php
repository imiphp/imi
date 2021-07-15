<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 切面注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property int $priority 优先级，越大越先执行
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Aspect extends Base
{
    public function __construct(?array $__data = null, int $priority = 0)
    {
        parent::__construct(...\func_get_args());
    }
}
