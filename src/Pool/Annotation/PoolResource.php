<?php

namespace Imi\Pool\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Parser;
use Imi\Pool\PoolManager;

/**
 * 连接池对象注入.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class PoolResource extends Inject
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
