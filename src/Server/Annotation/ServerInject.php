<?php

declare(strict_types=1);

namespace Imi\Server\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\App;
use Imi\Bean\Annotation\Inherit;
use Imi\RequestContext;

/**
 * 服务器容器对象注入
 * 使用：RequestContext::getServerBean().
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property string $name Bean名称或类名
 * @property array  $args Bean实例化参数
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ServerInject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

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
        if ($server = RequestContext::getServer())
        {
            return $server->getBean($this->name, ...$this->args);
        }

        return App::getBean($this->name, ...$this->args);
    }
}
