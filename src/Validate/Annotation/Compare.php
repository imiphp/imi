<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 比较验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 *
 * @property mixed  $value     被比较值
 * @property string $operation 比较符，使用顺序：name代表的值->比较符->被比较值；允许使用：==、!=、===、!==、<、<=、>、>=
 */
#[\Attribute]
class Compare extends Condition
{
    /**
     * @param mixed    $value
     * @param callable $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Validate\ValidatorHelper::compare', array $args = [
        '{:value}',
        '{operation}',
        '{value}',
    ], ?string $exception = null, ?int $exCode = null, $value = null, string $operation = '==')
    {
        parent::__construct(...\func_get_args());
    }
}
