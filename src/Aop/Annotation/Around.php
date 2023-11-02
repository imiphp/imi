<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 环绕通知.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Around extends Base
{
}
