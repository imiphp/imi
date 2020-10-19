<?php

namespace Imi\Server\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Parser;
use Imi\RequestContext;
use Imi\Util\Coroutine;

/**
 * 服务器容器对象注入
 * 使用：RequestContext::getServerBean().
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class ServerInject extends Inject
{
    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        if (Coroutine::isIn() && $server = RequestContext::getServer())
        {
            return $server->getBean($this->name, ...$this->args);
        }

        return App::getBean($this->name, ...$this->args);
    }
}
