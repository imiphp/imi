<?php

declare(strict_types=1);

namespace Imi\Queue\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Inherit;

/**
 * 队列类型的结构类型.
 *
 * @Annotation
 *
 * @Target({"CONST"})
 *
 * @property string $type
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
#[Inherit]
class QueueTypeStructType extends Base
{
    public function __construct(?array $__data = null, string $type = '')
    {
        parent::__construct(...\func_get_args());
    }
}
