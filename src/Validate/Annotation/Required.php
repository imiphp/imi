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
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Util\ObjectArrayHelper::exists';

    /**
     * 参数名数组.
     */
    public array $args = [
        '{:data}',
        '{name}',
    ];

    public function __construct(?array $__data = null, array $args = [
        '{:data}',
        '{name}',
    ])
    {
        parent::__construct(...\func_get_args());
    }
}
