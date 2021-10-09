<?php

declare(strict_types=1);

namespace Imi\Async\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 异步执行.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Async extends Base
{
}
