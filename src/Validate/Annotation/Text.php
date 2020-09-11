<?php

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 文本验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class Text extends Condition
{
    /**
     * 是否为字符模式，默认为 false
     * 设为 true 则使用字符判断长度
     * 设为 false 则使用字节判断长度.
     *
     * @var bool
     */
    public $char = false;

    /**
     * 最短长度.
     *
     * @var int
     */
    public $min = 0;

    /**
     * 最长长度，为null则不限制长度.
     *
     * @var int|null
     */
    public $max;

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::text';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public $args = [
        '{:value}',
        '{min}',
        '{max}',
        '{char}',
    ];
}
