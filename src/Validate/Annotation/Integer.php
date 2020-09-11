<?php

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 整数验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class Integer extends Condition
{
    /**
     * 最小值，为null不限制.
     *
     * @var int|null
     */
    public $min;

    /**
     * 最大值，为null不限制.
     *
     * @var int|null
     */
    public $max;

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::int';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public $args = [
        '{:value}',
        '{min}',
        '{max}',
    ];
}
