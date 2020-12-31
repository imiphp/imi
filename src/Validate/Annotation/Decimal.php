<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 小数验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class Decimal extends Condition
{
    /**
     * 最小值，为null不限制.
     *
     * @var float|null
     */
    public ?float $min = null;

    /**
     * 最大值，为null不限制.
     *
     * @var float|null
     */
    public ?float $max = null;

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
    public $callable = '\Imi\Validate\ValidatorHelper::decimal';

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
