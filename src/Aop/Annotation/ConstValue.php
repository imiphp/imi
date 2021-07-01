<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Inherit;

/**
 * 从常量中读取值
 *
 * 支持在注解中为属性动态赋值
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property string $name    常量名
 * @property mixed  $default 常量不存在时，返回的默认值
 */
#[\Attribute]
class ConstValue extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, string $name = '', $default = null)
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
        return \defined($this->name) ? \constant($this->name) : $this->default;
    }
}
