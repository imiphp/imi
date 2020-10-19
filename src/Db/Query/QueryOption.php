<?php

namespace Imi\Db\Query;

class QueryOption
{
    /**
     * 表名.
     *
     * @var \Imi\Db\Query\Interfaces\ITable
     */
    public $table;

    /**
     * distinct.
     *
     * @var bool
     */
    public $distinct = false;

    /**
     * 查询字段.
     *
     * @var array
     */
    public $field = [];

    /**
     * where 条件.
     *
     * @var \Imi\Db\Query\Interfaces\IBaseWhere[]
     */
    public $where = [];

    /**
     * join.
     *
     * @var \Imi\Db\Query\Interfaces\IJoin[]
     */
    public $join = [];

    /**
     * order by.
     *
     * @var \Imi\Db\Query\Interfaces\IOrder[]
     */
    public $order = [];

    /**
     * group by.
     *
     * @var \Imi\Db\Query\Interfaces\IGroup[]
     */
    public $group = [];

    /**
     * having.
     *
     * @var \Imi\Db\Query\Interfaces\IHaving[]
     */
    public $having = [];

    /**
     * 保存的数据.
     *
     * @var array|\Imi\Db\Query\Raw[]|\Imi\Db\Query\Interfaces\IQuery
     */
    public $saveData = [];

    /**
     * 记录从第几个开始取出.
     *
     * @var int
     */
    public $offset;

    /**
     * 查询几条记录.
     *
     * @var int
     */
    public $limit;

    /**
     * 锁配置.
     *
     * @var int|string|null
     */
    public $lock;

    public function __construct()
    {
        $this->table = new Table();
    }

    public function __clone()
    {
        $this->table = clone $this->table;
    }
}
