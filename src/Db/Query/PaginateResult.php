<?php
namespace Imi\Db\Query;

use Imi\Db\Interfaces\IStatement;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Interfaces\IPaginateResult;

class PaginateResult implements IPaginateResult
{
    /**
     * Statement
     *
     * @var \Imi\Db\Query\Interfaces\IResult
     */
    protected $statement;

    /**
     * 数组数据
     *
     * @var array
     */
    protected $arrayData;

    /**
     * 页码
     *
     * @var int
     */
    protected $page;

    /**
     * 查询几条记录
     *
     * @var int
     */
    protected $limit;

    /**
     * 记录总数
     *
     * @var int
     */
    protected $total;

    /**
     * 总页数
     *
     * @var int
     */
    protected $pageCount;

    /**
     * 自定义选项
     *
     * @var array
     */
    protected $options;

    public function __construct(IResult $statement, $page, $limit, $total, $pageCount, $options)
    {
        $this->statement = $statement;
        $this->page = $page;
        $this->limit = $limit;
        $this->total = $total;
        $this->options = $options;
        $this->pageCount = $pageCount;
    }

    /**
     * SQL是否执行成功
     * @return boolean
     */
    public function isSuccess(): bool
    {
        return $this->statement->isSuccess();
    }

    /**
     * 获取最后插入的ID
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->statement->getLastInsertId();
    }

    /**
     * 获取影响行数
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->statement->getAffectedRows();
    }

    /**
     * 返回一行数据，数组或对象
     * @param string $className 实体类名，为null则返回数组
     * @return mixed
     */
    public function get($className = null)
    {
        return $this->statement->get($className);
    }

    /**
     * 返回数组
     * @param string $className 实体类名，为null则数组每个成员为数组
     * @return array
     */
    public function getArray($className = null)
    {
        return $this->statement->getArray($className);
    }

    /**
     * 获取一列
     * @return array
     */
    public function getColumn($column = 0)
    {
        return $this->statement->getColumn($column);
    }

    /**
     * 获取标量结果
     * @return mixed
     */
    public function getScalar()
    {
        return $this->statement->getScalar();
    }

    /**
     * 获取记录行数
     * @return int
     */
    public function getRowCount()
    {
        return $this->statement->getRowCount();
    }

    /**
     * 获取执行的SQL语句
     *
     * @return string
     */
    public function getSql()
    {
        return $this->statement->getSql();
    }

    /**
     * 获取结果集对象
     *
     * @return \Imi\Db\Interfaces\IStatement
     */
    public function getStatement(): IStatement
    {
        return $this->statement->getStatement();
    }

    /**
     * 获取数组数据
     *
     * @return void
     */
    public function getList()
    {
        return $this->statement->getArray();
    }

    /**
     * 获取记录总数
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * 获取查询几条记录
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * 获取总页数
     *
     * @return int
     */
    public function getPageCount()
    {
        return $this->pageCount;
    }

    /**
     * 将当前对象作为数组返回
     * @return array
     */
    public function toArray(): array
    {
        if(null === $this->arrayData)
        {
            $this->arrayData = [
                // 数据列表
                $this->options['field_list'] ?? 'list'              =>  $this->statement->getArray(),
                // 每页记录数
                $this->options['field_limit'] ?? 'limit'            =>  $this->limit,
            ];
            if(null !== $this->total)
            {
                // 记录总数
                $this->arrayData[$this->options['field_total'] ?? 'total'] = $this->total;
                // 总页数
                $this->arrayData[$this->options['field_page_count'] ?? 'page_count'] = $this->pageCount;
            }
        }
        return $this->arrayData;
    }

    /**
     * json 序列化
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

}