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
 *
 * @property int|float|null $min      最小值，为null不限制
 * @property int|float|null $max      最大值，为null不限制
 * @property int|null       $accuracy 小数精度位数，为null不限制
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class Number extends Condition
{
    /**
     * @param int|float|null $min
     * @param int|float|null $max
     * @param callable       $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Validate\ValidatorHelper::number', array $args = [
        '{:value}',
        '{min}',
        '{max}',
        '{accuracy}',
    ], ?string $exception = null, ?int $exCode = null, $min = null, $max = null, ?int $accuracy = null)
    {
        parent::__construct(...\func_get_args());
    }
}
