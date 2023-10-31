<?php

declare(strict_types=1);

namespace Imi\Queue\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Inherit;

/**
 * 注入队列对象
 *
 * @Annotation
 *
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property string $type
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class QueueTypeStructType extends Base
{
    public function __construct(?array $__data = null, string $type = '')
    {
        parent::__construct(...\func_get_args());
    }
}
