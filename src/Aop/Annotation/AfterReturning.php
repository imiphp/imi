<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 在After之后、return之前触发.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class AfterReturning extends Base
{
}
