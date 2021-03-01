<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 列表验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class InList extends Condition
{
    /**
     * 列表.
     *
     * @var array
     */
    public array $list = [];

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::in';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public array $args = [
        '{:value}',
        '{list}',
    ];

    public function __construct(?array $__data = null, array $list = [], array $args = [
        '{:value}',
        '{list}',
    ])
    {
        parent::__construct(...\func_get_args());
    }
}
