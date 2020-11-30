<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\Bean\Annotation\Parser;
use Imi\RequestContext;
use Imi\Util\Coroutine;

/**
 * 属性注入
 * 使用：RequestContext::getBean().
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class RequestInject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * Bean名称或类名.
     */
    public $name;

    /**
     * Bean实例化参数.
     *
     * @var array
     */
    public $args = [];

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
