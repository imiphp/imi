<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 文本验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Text extends Condition
{
    /**
     * 是否为字符模式，默认为 false
     * 设为 true 则使用字符判断长度
     * 设为 false 则使用字节判断长度.
     */
    public bool $char = false;

    /**
     * 最短长度.
     */
    public int $min = 0;

    /**
     * 最长长度，为null则不限制长度.
     */
    public ?int $max = null;

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::text';

    /**
     * 参数名数组.
     */
    public array $args = [
        '{:value}',
        '{min}',
        '{max}',
        '{char}',
    ];

    public function __construct(?array $__data = null, bool $char = false, int $min = 0, ?int $max = null, array $args = [
        '{:value}',
        '{min}',
        '{max}',
        '{char}',
    ])
    {
        parent::__construct(...\func_get_args());
    }
}
