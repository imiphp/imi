<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Inherit;
use Imi\RequestContext;

/**
 * 属性注入
 * 使用：RequestContext::getBean().
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class RequestInject extends BaseInjectValue
{
    public function __construct(
        /**
         * Bean名称或类名.
         */
        public string $name = '',
        /**
         * Bean实例化参数.
         */
        public array $args = []
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getRealValue()
    {
        return RequestContext::getBean($this->name, ...$this->args);
    }
}
