<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IFullTextOptions
{
    /**
     * 获取字段名.
     *
     * @return string[]
     */
    public function getFieldNames(): array;

    /**
     * 设置字段名.
     *
     * @param string|string[] $fieldNames
     *
     * @return static
     */
    public function setFieldNames($fieldNames): self;

    /**
     * 获取搜索内容.
     */
    public function getSearchText(): string;

    /**
     * 设置搜索内容.
     *
     * @return static
     */
    public function setSearchText(string $searchText): self;

    /**
     * 获取最小分数.
     *
     * @return static
     */
    public function setMinScore(float $minScore): self;

    /**
     * 获取最小分数.
     */
    public function getMinScore(): float;

    /**
     * 获取分数字段名.
     *
     * 如果是 null 则查询不分数返回字段.
     */
    public function getScoreFieldName(): ?string;

    /**
     * 设置分数字段名.
     *
     * @return static
     */
    public function setScoreFieldName(?string $scoreFieldName): self;

    /**
     * 获取分数排序顺序.
     *
     * 如果是 null 则不排序
     */
    public function getOrderDirection(): ?string;

    /**
     * 设置分数排序顺序.
     *
     * @return static
     */
    public function setOrderDirection(?string $orderDirection): self;

    /**
     * 获取查询条件的逻辑运算符.
     *
     * 如果是 null 则不加上查询条件.
     */
    public function getWhereLogicalOperator(): ?string;

    /**
     * 设置查询条件的逻辑运算符.
     *
     * @return static
     */
    public function setWhereLogicalOperator(?string $whereLogicalOperator): self;

    /**
     * 转为查询条件字符串.
     */
    public function toWhereSql(IQuery $query): string;

    /**
     * 转为分数查询条件字符串.
     */
    public function toScoreSql(IQuery $query): string;

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array;
}
