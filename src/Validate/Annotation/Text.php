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
 *
 * @property bool     $char 是否为字符模式，默认为 false；设为 true 则使用字符判断长度；设为 false 则使用字节判断长度
 * @property int      $min  最短长度
 * @property int|null $max  最长长度，为null则不限制长度
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class Text extends Condition
{
    /**
     * @param callable $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Validate\ValidatorHelper::text', array $args = [
        '{:value}',
        '{min}',
        '{max}',
        '{char}',
    ], ?string $exception = null, ?int $exCode = null, bool $char = false, int $min = 0, ?int $max = null)
    {
        parent::__construct(...\func_get_args());
    }
}
