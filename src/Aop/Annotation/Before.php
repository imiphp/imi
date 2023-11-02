<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 前置操作注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Before extends Base
{
}
