<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Inherit;

/**
 * 回调注解，返回该回调的返回值
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class CallableValue extends BaseInjectValue
{
    public function __construct(
        /**
         * 回调.
         *
         * @var callable
         */
        public $callable = null
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getRealValue(): mixed
    {
        return ($this->callable)();
    }
}
