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
    public string $name = '';

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
        if ($server = RequestContext::getServer())
        {
            return $server->getBean($this->name, ...$this->args);
        }

        return App::getBean($this->name, ...$this->args);
    }
}
