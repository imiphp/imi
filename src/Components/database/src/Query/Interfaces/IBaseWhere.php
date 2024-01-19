<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IBaseWhere extends IBase
{
    /**
     * 获取无逻辑的字符串.
     */
    public function toStringWithoutLogic(IQuery $query): string;

    /**
     * 逻辑运算符.
     */
    public function getLogicalOperator(): string;

    /**
     * 逻辑运算符.
     */
    public function setLogicalOperator(string $logicalOperator): void;
}
