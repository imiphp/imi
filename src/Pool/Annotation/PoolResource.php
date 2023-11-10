<?php

declare(strict_types=1);

namespace Imi\Pool\Annotation;

use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Annotation\Inherit;
use Imi\Pool\PoolManager;

/**
 * 连接池对象注入.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class PoolResource extends RequestInject
{
    /**
     * 获取注入值的真实值
     */
    public function getRealValue(): mixed
    {
        return PoolManager::getRequestContextResource($this->name);
    }
}
