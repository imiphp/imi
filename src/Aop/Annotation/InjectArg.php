<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 方法参数注入.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Bean\Parser\NullParser")
 *
 * @property string $name  参数名
 * @property mixed  $value 注入的值
 */
#[\Attribute]
class InjectArg extends Base
{
    /**
     * @param mixed $value
     */
    public function __construct(?array $__data = null, string $name = '', $value = null)
    {
        parent::__construct(...\func_get_args());
    }
}
