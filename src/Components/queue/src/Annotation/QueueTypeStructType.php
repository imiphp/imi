<?php

declare(strict_types=1);

namespace Imi\Queue\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Inherit;

/**
 * 队列类型的结构类型.
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
#[Inherit]
class QueueTypeStructType extends Base
{
    public function __construct(public string $type = '')
    {
    }
}
