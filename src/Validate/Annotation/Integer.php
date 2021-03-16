<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 整数验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Integer extends Condition
{
    /**
     * 最小值，为null不限制.
     */
    public ?int $min = null;

    /**
     * 最大值，为null不限制.
     */
    public ?int $max = null;

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::int';

    /**
     * 参数名数组.
     */
    public array $args = [
        '{:value}',
        '{min}',
        '{max}',
    ];

    public function __construct(?array $__data = null, ?int $min = null, ?int $max = null, array $args = [
        '{:value}',
        '{min}',
        '{max}',
    ])
    {
        parent::__construct(...\func_get_args());
    }
}
