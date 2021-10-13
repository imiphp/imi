<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\Bean\Annotation\Inherit;

/**
 * 对象注入
 * 使用：App::getBean().
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property string $name Bean名称或类名
 * @property array  $args Bean实例化参数
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Inject extends BaseInjectValue
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
        return App::getBean($this->name, ...$this->args);
    }
}
