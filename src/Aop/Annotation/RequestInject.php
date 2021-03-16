<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\Bean\Annotation\Inherit;
use Imi\RequestContext;
use Imi\Swoole\Util\Coroutine;

/**
 * 属性注入
 * 使用：RequestContext::getBean().
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute]
class RequestInject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * Bean名称或类名.
     */
    public ?string $name = null;

    /**
     * Bean实例化参数.
     */
    public array $args = [];

    public function __construct(?array $__data = null, string $name = '', array $args = [])
    {
        parent::__construct(...\func_get_args());
    }

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        if (Coroutine::isIn())
        {
            return RequestContext::getBean($this->name, ...$this->args);
        }
        else
        {
            return App::getBean($this->name, ...$this->args);
        }
    }
}
