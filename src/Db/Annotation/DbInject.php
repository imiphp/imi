<?php

declare(strict_types=1);

namespace Imi\Db\Annotation;

use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Annotation\Parser;
use Imi\Db\Db;
use Imi\Db\Query\QueryType;

/**
 * 连接池对象注入.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class DbInject extends RequestInject
{
    /**
     * 查询类型.
     *
     * @var int
     */
    public $queryType = QueryType::WRITE;

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
