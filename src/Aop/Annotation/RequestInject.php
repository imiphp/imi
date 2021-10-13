<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Inherit;
use Imi\RequestContext;

/**
 * 属性注入
 * 使用：RequestContext::getBean().
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property string $name Bean名称或类名
 * @property array  $args Bean实例化参数
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class RequestInject extends BaseInjectValue
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, string $name = '', array $args = [])
    {
        parent::__construct(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getRealValue()
    {
        return RequestContext::getBean($this->name, ...$this->args);
    }
}
