<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 指定允许继承父类的指定注解.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY", "CONST"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Inherit extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'annotation';

    /**
     * 允许的注解类，为 null 则不限制，支持字符串或数组.
     *
     * @var string|string[]|null
     */
    public $annotation = null;

    /**
     * @param array|null           $__data
     * @param string|string[]|null $annotation
     */
    public function __construct(?array $__data = null, $annotation = null)
    {
        parent::__construct(...\func_get_args());
    }
}
