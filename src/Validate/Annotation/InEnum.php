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
 */
#[\Attribute]
class InEnum extends Condition
{
    /**
     * 注解类名.
     */
    public string $enum = '';

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::inEnum';

    /**
     * 参数名数组.
     */
    public array $args = [
        '{:value}',
        '{enum}',
    ];

    public function __construct(?array $__data = null, string $enum = '', array $args = [
        '{:value}',
        '{enum}',
    ])
    {
        parent::__construct(...\func_get_args());
    }
}
