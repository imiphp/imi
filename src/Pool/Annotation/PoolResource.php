<?php

declare(strict_types=1);

namespace Imi\Pool\Annotation;

use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Annotation\Parser;
use Imi\Pool\PoolManager;

/**
 * 连接池对象注入.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
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
