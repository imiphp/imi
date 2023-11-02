<?php

declare(strict_types=1);

namespace Imi\Test\Component\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 异步执行.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class CommentAnnotation extends Base
{
}
