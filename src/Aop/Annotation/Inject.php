<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\Bean\Annotation\Inherit;

/**
 * 对象注入
 * 使用：App::getBean().
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class Inject extends BaseInjectValue
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
        return App::getBean($this->name, ...$this->args);
    }
}
