<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\Bean\Annotation\Parser;
use Imi\RequestContext;
use Imi\Swoole\Util\Coroutine;

/**
 * 属性注入
 * 使用：RequestContext::getBean().
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class RequestInject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * Bean名称或类名.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Bean实例化参数.
     *
     * @var array
     */
    public array $args = [];

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
