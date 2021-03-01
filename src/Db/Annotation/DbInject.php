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
#[\Attribute]
class DbInject extends RequestInject
{
    /**
     * 查询类型.
     *
     * @var int
     */
    public int $queryType = QueryType::WRITE;

    public function __construct(?array $__data = null, int $queryType = QueryType::WRITE)
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
        return Db::getInstance($this->name, $this->queryType);
    }
}
