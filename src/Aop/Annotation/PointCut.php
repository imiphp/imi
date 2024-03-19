<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\Base;

/**
 * 切入点.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class PointCut extends Base
{
    public function __construct(
        /**
         * 切入点类型，PointCutType::XXX.
         */
        public int $type = PointCutType::METHOD,
        /**
         * 允许的切入点.
         */
        public array $allow = [],
        /**
         * 不允许的切入点，即使包含中有的，也可以被排除.
         */
        public array $deny = [],
        /**
         * 优先级，越大越先执行.
         * 为 null 时使用 Aspect 设置.
         */
        public ?int $priority = null
    ) {
    }
}
