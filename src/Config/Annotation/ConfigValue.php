<?php

declare(strict_types=1);

namespace Imi\Config\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Bean\Annotation\Inherit;
use Imi\Config;

/**
 * 从配置中读取值
 *
 * 支持在注解中为属性动态赋值
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class ConfigValue extends BaseInjectValue
{
    public function __construct(
        /**
         * 配置名，支持@app、@currentServer等用法.
         */
        public string $name = '',
        /**
         * 配置不存在时，返回的默认值
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
        return Config::get($this->name, $this->default);
    }
}
