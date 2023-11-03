<?php

declare(strict_types=1);

namespace Imi\Config\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Bean\Annotation\Inherit;

use function Imi\env;

/**
 * 从环境变量中读取值
 *
 * 支持在注解中为属性动态赋值
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class EnvValue extends BaseInjectValue
{
    public function __construct(
        /**
         * 环境变量名称.
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
        return env($this->name, $this->default);
    }
}
