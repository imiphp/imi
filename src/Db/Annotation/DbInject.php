<?php

declare(strict_types=1);

namespace Imi\Db\Annotation;

use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Annotation\Inherit;
use Imi\Db\Db;
use Imi\Db\Query\QueryType;

/**
 * 连接池对象注入.
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class DbInject extends RequestInject
{
    /**
     * 查询类型.
     *
     * @var int
     */
    public int $queryType = QueryType::WRITE;

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return Db::getInstance($this->name, $this->queryType);
    }
}
