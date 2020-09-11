<?php

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 枚举验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class InEnum extends Condition
{
    /**
     * 注解类名.
     *
     * @var string
     */
    public $enum;

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::inEnum';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public $args = [
        '{:value}',
        '{enum}',
    ];
}
