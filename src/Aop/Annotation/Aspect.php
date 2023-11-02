<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 切面注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Aspect extends Base
{
    public function __construct(
        /**
         * 优先级，越大越先执行.
         */
        public int $priority = 0
    ) {
    }
}
