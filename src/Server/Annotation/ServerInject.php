<?php

declare(strict_types=1);

namespace Imi\Server\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;
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
class ServerInject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
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
        if (Coroutine::isIn() && $server = RequestContext::getServer())
        {
            return $server->getBean($this->name, ...$this->args);
        }

        return App::getBean($this->name, ...$this->args);
    }
}
