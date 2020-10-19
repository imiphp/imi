<?php

namespace Imi\Redis\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Parser;
use Imi\Redis\RedisManager;

/**
 * 连接池对象注入.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class RedisInject extends Inject
{
    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return RedisManager::getInstance($this->name);
    }
}
