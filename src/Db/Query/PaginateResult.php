<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Interfaces\IStatement;
use Imi\Db\Query\Interfaces\IPaginateResult;
use Imi\Db\Query\Interfaces\IResult;

class PaginateResult implements IPaginateResult
{
    /**
     * 数据库查询结构.
     *
     * @var \Imi\Db\Query\Interfaces\IResult
     */
    protected IResult $result;

    /**
     * 数组数据.
     *
     * @var array|null
     */
    protected ?array $arrayData = null;

    /**
     * 页码
     *
     * @var int
     */
    protected int $page;

    /**
     * 查询几条记录.
     *
     * @var int
     */
    protected int $limit;

    /**
     * 记录总数.
     *
     * @var int|null
     */
    protected ?int $total;

    /**
     * 总页数.
     *
     * @var int|null
     */
    protected ?int $pageCount;

    /**
     * 自定义选项.
     *
     * @var array
     */
    protected array $options;

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
     * SQL是否执行成功
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->result->isSuccess();
    }

    /**
     * 获取最后插入的ID.
     *
     * @return int|string
     */
    public function getLastInsertId()
    {
        return $this->result->getLastInsertId();
    }

    /**
     * 获取影响行数.
     *
     * @return int
     */
    public function getAffectedRows(): int
    {
        return $this->result->getAffectedRows();
    }

    /**
     * 返回一行数据，数组或对象
     *
     * @param string|null $className 实体类名，为null则返回数组
     *
     * @return mixed
     */
    public function get(?string $className = null)
    {
        return $this->result->get($className);
    }

    /**
     * 返回数组.
     *
     * @param string|null $className 实体类名，为null则数组每个成员为数组
     *
     * @return array
     */
    public function getArray(?string $className = null): array
    {
        return $this->result->getArray($className);
    }

    /**
     * 获取一列.
     *
     * @return array
     */
    public function getColumn($column = 0): array
    {
        return $this->result->getColumn($column);
    }

    /**
     * 获取标量结果.
     *
     * @return mixed
     */
    public function getScalar()
    {
        return $this->result->getScalar();
    }

    /**
     * 获取记录行数.
     *
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->result->getRowCount();
    }

    /**
     * 获取执行的SQL语句.
     *
     * @return string
     */
    public function getSql(): string
    {
        return $this->result->getSql();
    }

    /**
     * 获取结果集对象
     *
     * @return \Imi\Db\Interfaces\IStatement
     */
    public function getStatement(): IStatement
    {
        return $this->result->getStatement();
    }

    /**
     * 获取数组数据.
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->result->getArray();
    }

    /**
     * 获取记录总数.
     *
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * 获取查询几条记录.
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * 获取总页数.
     *
     * @return int|null
     */
    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    /**
     * 将当前对象作为数组返回.
     *
     * @return array
     */
    public function toArray(): array
    {
        $arrayData = &$this->arrayData;
        if (null === $arrayData)
        {
            $options = $this->options;
            $arrayData = [
                // 数据列表
                $options['field_list'] ?? 'list'              => $this->result->getArray(),
                // 每页记录数
                $options['field_limit'] ?? 'limit'            => $this->limit,
            ];
            if (null !== $this->total)
            {
                // 记录总数
                $arrayData[$options['field_total'] ?? 'total'] = $this->total;
                // 总页数
                $arrayData[$options['field_page_count'] ?? 'page_count'] = $this->pageCount;
            }
        }

        return $arrayData;
    }

    /**
     * json 序列化.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
