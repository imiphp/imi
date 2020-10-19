<?php

namespace Imi\Bean\Annotation;

/**
 * 指定允许继承父类的指定注解.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY", "CONST"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class Inherit extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'annotation';

    /**
     * 允许的注解类，为 null 则不限制，支持字符串或数组.
     *
     * @var string|string[]
     */
    public $annotation;
}
