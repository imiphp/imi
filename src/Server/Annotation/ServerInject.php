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
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class ServerInject extends BaseInjectValue
{
    public function __construct(
        /**
         * Bean名称或类名.
         */
        public string $name = '',
        /**
         * Bean实例化参数.
         */
        public array $args = []
    ) {
    }

    /**
     * 获取注入值的真实值
     */
    public function getRealValue(): mixed
    {
        if ($server = RequestContext::getServer())
        {
            return $server->getBean($this->name, ...$this->args);
        }

        return App::getBean($this->name, ...$this->args);
    }
}
