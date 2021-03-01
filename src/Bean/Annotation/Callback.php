<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;

/**
 * 回调注解.
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute]
class Callback extends BaseInjectValue
{
    /**
     * 类名，或者传入对象
     *
     * @var string|object
     */
    public $class;

    /**
     * 方法名.
     *
     * @var string
     */
    public string $method = '';

    public function __construct(?array $__data = null, $class = null, string $method = '')
    {
        parent::__construct(...\func_get_args());
    }

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return [$this->class, $this->method];
    }
}
