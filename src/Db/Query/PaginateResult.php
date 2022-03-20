<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Config;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Query\Interfaces\IPaginateResult;
use Imi\Db\Query\Interfaces\IResult;

class PaginateResult implements IPaginateResult
{
    /**
     * 数据库查询结构.
     */
    protected IResult $result;

    /**
     * 数组数据.
     */
    protected ?array $arrayData = null;

    /**
     * 页码
     */
    protected int $page = 0;

    /**
     * 查询几条记录.
     */
    protected int $limit = 0;

    /**
     * 记录总数.
     */
    protected ?int $total = null;

    /**
     * 总页数.
     */
    protected ?int $pageCount = null;

    /**
     * 自定义选项.
     */
    protected array $options = [];

    protected ?array $data = null;

    public function __construct(IResult $result, int $page, int $limit, ?int $total, ?int $pageCount, array $options)
    {
        $this->result = $result;
        $this->page = $page;
        $this->limit = $limit;
        $this->total = $total;
        $this->options = $options;
        $this->pageCount = $pageCount;
    }

    /**
     * {@inheritDoc}
     */
    public function isSuccess(): bool
    {
        return $this->result->isSuccess();
    }

    /**
     * {@inheritDoc}
     */
    public function getLastInsertId()
    {
        return $this->result->getLastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function getAffectedRows(): int
    {
        return $this->result->getAffectedRows();
    }

    /**
     * {@inheritDoc}
     */
    public function get(?string $className = null)
    {
        return $this->result->get($className);
    }

    /**
     * {@inheritDoc}
     */
    public function getArray(?string $className = null): array
    {
        if (null === $className)
        {
            return $this->getList();
        }

        return $this->result->getArray($className);
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn($column = 0): array
    {
        return $this->result->getColumn($column);
    }

    /**
     * {@inheritDoc}
     */
    public function getScalar($columnKey = 0)
    {
        return $this->result->getScalar($columnKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getRowCount(): int
    {
        return $this->result->getRowCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getSql(): string
    {
        return $this->result->getSql();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatement(): IStatement
    {
        return $this->result->getStatement();
    }

    /**
     * {@inheritDoc}
     */
    public function getList(): array
    {
        return $this->data ??= $this->result->getArray();
    }

    /**
     * {@inheritDoc}
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * {@inheritDoc}
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * {@inheritDoc}
     */
    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $arrayData = &$this->arrayData;
        if (null === $arrayData)
        {
            $options = $this->options;
            $fields = Config::get('@app.db.paginate.fields', []);
            $arrayData = [
                // 数据列表
                $options['field_list'] ?? ($fields['list'] ?? 'list')   => $this->getList(),
                // 每页记录数
                $options['field_limit'] ?? ($fields['limit'] ?? 'limit') => $this->limit,
            ];
            if (null !== $this->total)
            {
                // 记录总数
                $arrayData[$options['field_total'] ?? ($fields['total'] ?? 'total')] = $this->total;
                // 总页数
                $arrayData[$options['field_page_count'] ?? ($fields['pageCount'] ?? 'page_count')] = $this->pageCount;
            }
        }

        return $arrayData;
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getStatementRecords(): array
    {
        return $this->result->getStatementRecords();
    }
}
