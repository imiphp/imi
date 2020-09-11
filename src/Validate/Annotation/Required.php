<?php

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 必选参数.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class Required extends Condition
{
    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Util\ObjectArrayHelper::exists';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public $args = [
        '{:data}',
        '{name}',
    ];
}
