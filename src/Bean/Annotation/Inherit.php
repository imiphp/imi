<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 指定允许继承父类的指定注解.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY", "CONST"})
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|string[]|null $annotation 允许的注解类，为 null 则不限制，支持字符串或数组
 */
#[\Attribute]
class Inherit extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'annotation';

    /**
     * @param string|string[]|null $annotation
     */
    public function __construct(?array $__data = null, $annotation = null)
    {
        parent::__construct(...\func_get_args());
    }
}
