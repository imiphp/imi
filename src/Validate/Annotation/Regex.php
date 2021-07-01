<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 正则验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 *
 * @property string $pattern 正则表达式文本
 */
#[\Attribute]
class Regex extends Condition
{
    /**
     * @param callable $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Validate\ValidatorHelper::regex', array $args = [
        '{:value}',
        '{pattern}',
    ], ?string $exception = null, ?int $exCode = null, string $pattern = '')
    {
        parent::__construct(...\func_get_args());
    }
}
