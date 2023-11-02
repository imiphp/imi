<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;

/**
 * 回调注解.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[\Imi\Bean\Annotation\Inherit]
class Callback extends BaseInjectValue
{
    public function __construct(
        /**
         * 类名，或者传入对象
         *
         * @var string|object
         */
        public $class = null,
        /**
         * 方法名.
         */
        public string $method = ''
    ) {
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
