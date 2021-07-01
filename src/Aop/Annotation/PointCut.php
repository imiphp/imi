<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 切入点.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property int   $type  切入点类型，PointCutType::XXX
 * @property array $allow 允许的切入点
 * @property array $deny  不允许的切入点，即使包含中有的，也可以被排除
 */
#[\Attribute]
class PointCut extends Base
{
    public function __construct(?array $__data = null, int $type = PointCutType::METHOD, array $allow = [], array $deny = [])
    {
        parent::__construct(...\func_get_args());
    }
}
