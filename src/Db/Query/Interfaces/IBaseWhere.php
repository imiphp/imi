<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IBaseWhere extends IBase
{
    /**
     * 获取无逻辑的字符串.
     *
     * @param IQuery $query
     *
     * @return string
     */
    public function toStringWithoutLogic(IQuery $query): string;

    /**
     * 逻辑运算符.
     *
     * @return string
     */
    public function getLogicalOperator(): string;

    /**
     * 逻辑运算符.
     *
     * @param string $logicalOperator
     *
     * @return void
     */
    public function setLogicalOperator(string $logicalOperator);
}
