<?php

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 切面注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class Aspect extends Base
{
    /**
     * 优先级，越大越先执行.
     *
     * @var int
     */
    public $priority = 0;
}
