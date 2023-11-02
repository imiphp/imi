<?php

declare(strict_types=1);

namespace Imi\Async\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 延后执行.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Defer extends Base
{
}
