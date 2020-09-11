<?php

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
class RequestInject extends Inject
{
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
