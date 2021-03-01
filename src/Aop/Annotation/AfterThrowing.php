<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 在异常时通知.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class AfterThrowing extends Base
{
    /**
     * 允许捕获的异常类列表.
     *
     * @var array
     */
    public array $allow = [];

    /**
     * 不允许捕获的异常类列表.
     *
     * @var array
     */
    public array $deny = [];

    public function __construct(?array $__data = null, array $allow = [], array $deny = [])
    {
        parent::__construct(...\func_get_args());
    }
}
