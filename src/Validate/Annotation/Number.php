<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 数值验证，允许整数和小数.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class Number extends Condition
{
    /**
     * 最小值，为null不限制.
     *
     * @var int|float|null
     */
    public $min = null;

    /**
     * 最大值，为null不限制.
     *
     * @var int|float|null
     */
    public $max = null;

    /**
     * 小数精度位数，为null不限制.
     *
     * @var int
     */
    public ?int $accuracy = null;

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::number';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public array $args = [
        '{:value}',
        '{min}',
        '{max}',
        '{accuracy}',
    ];
}
