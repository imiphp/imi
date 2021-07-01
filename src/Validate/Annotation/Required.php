<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 必选参数.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Required extends Condition
{
    /**
     * @param callable $callable
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', $callable = '\Imi\Util\ObjectArrayHelper::exists', array $args = [
        '{:data}',
        '{name}',
    ], ?string $exception = null, ?int $exCode = null)
    {
        parent::__construct(...\func_get_args());
    }
}
