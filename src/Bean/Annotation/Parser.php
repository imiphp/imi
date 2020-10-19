<?php

namespace Imi\Bean\Annotation;

/**
 * 指定注解类处理器.
 *
 * @Annotation
 * @Target("CLASS")
 */
class Parser extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'className';

    /**
     * 处理器类名.
     *
     * @var string
     */
    public $className;
}
