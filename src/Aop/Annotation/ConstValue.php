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
 */
class ConstValue extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 常量名.
     *
     * @var string
     */
    public string $name = '';

    /**
     * 常量不存在时，返回的默认值
     *
     * @var mixed
     */
    public $default = null;

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
