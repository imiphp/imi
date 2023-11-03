<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Inherit;

/**
 * 从常量中读取值
 *
 * 支持在注解中为属性动态赋值
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class ConstValue extends BaseInjectValue
{
    public function __construct(
        /**
         * 常量名.
         */
        public string $name = '',
        /**
         * 常量不存在时，返回的默认值
         *
         * @var mixed
         */
        public $default = null
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getRealValue(): mixed
    {
        return \defined($this->name) ? \constant($this->name) : $this->default;
    }
}
