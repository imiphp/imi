<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 在异常时通知.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @property array $allow 允许捕获的异常类列表
 * @property array $deny  不允许捕获的异常类列表
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class AfterThrowing extends Base
{
    public function __construct(?array $__data = null, array $allow = [], array $deny = [])
    {
        parent::__construct(...\func_get_args());
    }
}
