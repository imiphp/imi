<?php

declare(strict_types=1);

namespace Imi\Pool\Annotation;

use Imi\Aop\Annotation\RequestInject;
use Imi\Pool\PoolManager;

/**
 * 连接池对象注入.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PoolResource extends RequestInject
{
    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return PoolManager::getRequestContextResource($this->name);
    }
}
