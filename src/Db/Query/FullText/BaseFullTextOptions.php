<?php

declare(strict_types=1);

namespace Imi\Db\Query\FullText;

use Imi\Db\Query\Interfaces\IFullTextOptions;

abstract class BaseFullTextOptions implements IFullTextOptions
{
    /**
     * 字段名.
     */
    protected array $fieldNames = [];

    /**
     * 搜索内容.
     */
    protected string $searchText = '';

    /**
     * 最小分数.
     */
    protected float $minScore = 0;

    /**
     * 分数字段名.
     */
    protected ?string $scoreFieldName = null;

    /**
     * 分数排序顺序.
     */
    protected ?string $orderDirection = null;

    /**
     * 查询条件的逻辑运算符.
     */
    protected ?string $whereLogicalOperator = 'and';

    /**
     * 绑定的数据们.
     */
    protected array $binds = [];

    /**
     * {@inheritDoc}
     */
    public function getFieldNames(): array
    {
        return $this->fieldNames;
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldNames($fieldNames): self
    {
        $this->fieldNames = (array) $fieldNames;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchText(): string
    {
        return $this->searchText;
    }

    /**
     * {@inheritDoc}
     */
    public function setSearchText(string $searchText): self
    {
        $this->searchText = $searchText;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMinScore(): float
    {
        return $this->minScore;
    }

    /**
     * {@inheritDoc}
     */
    public function setMinScore(float $minScore): self
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getBinds(): array
    {
        return $this->binds;
    }

    /**
     * {@inheritDoc}
     */
    public function getScoreFieldName(): ?string
    {
        return $this->scoreFieldName;
    }

    /**
     * {@inheritDoc}
     */
    public function setScoreFieldName(?string $scoreFieldName): self
    {
        $this->scoreFieldName = $scoreFieldName;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    /**
     * {@inheritDoc}
     */
    public function setOrderDirection(?string $orderDirection): self
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getWhereLogicalOperator(): ?string
    {
        return $this->whereLogicalOperator;
    }

    /**
     * {@inheritDoc}
     */
    public function setWhereLogicalOperator(?string $whereLogicalOperator): self
    {
        $this->whereLogicalOperator = $whereLogicalOperator;

        return $this;
    }
}
