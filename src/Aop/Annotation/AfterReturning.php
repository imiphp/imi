<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 在After之后、return之前触发.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class AfterReturning extends Base
{
}
