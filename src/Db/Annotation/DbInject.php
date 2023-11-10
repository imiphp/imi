<?php

declare(strict_types=1);

namespace Imi\Db\Annotation;

use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Annotation\Inherit;
use Imi\Db\Db;
use Imi\Db\Query\QueryType;

/**
 * 连接池对象注入.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class DbInject extends RequestInject
{
    public function __construct(
        public string $name = '',
        public array $args = [],
        /**
         * 查询类型.
         */
        public int $queryType = QueryType::WRITE
    ) {
    }

    /**
     * 获取注入值的真实值
     */
    public function getRealValue(): mixed
    {
        return Db::getInstance($this->name, $this->queryType);
    }
}
