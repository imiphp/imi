<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;

/**
 * 小数验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 *
 * @property float|null $min      最小值，为null不限制
 * @property float|null $max      最大值，为null不限制
 * @property int|null   $accuracy 小数精度位数，为null不限制
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class Decimal extends Condition
{
    /**
     * @param callable $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Validate\ValidatorHelper::decimal', array $args = [
        '{:value}',
        '{min}',
        '{max}',
        '{accuracy}',
    ], ?string $exception = null, ?int $exCode = null, ?float $min = null, ?float $max = null, ?int $accuracy = null)
    {
        parent::__construct(...\func_get_args());
    }
}
