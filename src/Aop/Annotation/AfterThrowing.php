<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 在异常时通知.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class AfterThrowing extends Base
{
    public function __construct(
        /**
         * 允许捕获的异常类列表.
         */
        public array $allow = [],
        /**
         * 不允许捕获的异常类列表.
         */
        public array $deny = []
    ) {
    }
}
