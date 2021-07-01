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
 *
 * @property int|null $min 最小值，为null不限制
 * @property int|null $max 最大值，为null不限制
 */
#[\Attribute]
class Integer extends Condition
{
    /**
     * @param callable $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Validate\ValidatorHelper::int', array $args = [
        '{:value}',
        '{min}',
        '{max}',
    ], ?string $exception = null, ?int $exCode = null, ?int $min = null, ?int $max = null)
    {
        parent::__construct(...\func_get_args());
    }
}
