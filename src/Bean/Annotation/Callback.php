<?php

namespace Imi\Bean\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;

/**
 * 回调注解.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
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
    public $method;

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
