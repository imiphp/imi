<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Inherit;

/**
 * 回调注解，返回该回调的返回值
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property callable $callable 回调
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class CallableValue extends BaseInjectValue
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'callable';

    public function __construct(?array $__data = null, callable $callable = null)
    {
        parent::__construct(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getRealValue()
    {
        return ($this->callable)();
    }
}
