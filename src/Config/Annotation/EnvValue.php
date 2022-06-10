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
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property string $name    环境变量名称
 * @property mixed  $default 配置不存在时，返回的默认值
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EnvValue extends BaseInjectValue
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, string $name = '', $default = null)
    {
        parent::__construct(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getRealValue()
    {
        return env($this->name, $this->default);
    }
}
