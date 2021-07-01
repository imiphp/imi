<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 枚举验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 *
 * @property string $enum 注解类名
 */
#[\Attribute]
class InEnum extends Condition
{
    /**
     * @param callable $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Validate\ValidatorHelper::inEnum', array $args = [
        '{:value}',
        '{enum}',
    ], ?string $exception = null, ?int $exCode = null, string $enum = '')
    {
        parent::__construct(...\func_get_args());
    }
}
