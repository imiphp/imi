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
 *
 * @property string|object $class  类名，或者传入对象
 * @property string        $method 方法名
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Callback extends BaseInjectValue
{
    /**
     * @param string|object $class
     */
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
