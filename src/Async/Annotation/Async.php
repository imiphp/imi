<?php

declare(strict_types=1);

namespace Imi\Async\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 异步执行.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Async extends Base
{
}
