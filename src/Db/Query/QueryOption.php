<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\ITable;

class QueryOption
{
    /**
     * 表名.
     */
    public ITable $table;

    /**
     * distinct.
     */
    public bool $distinct = false;

    /**
     * 查询字段.
     */
    public array $field = [];

    /**
     * where 条件.
     *
     * @var \Imi\Db\Query\Interfaces\IBaseWhere[]
     */
    public array $where = [];

    /**
     * join.
     *
     * @var \Imi\Db\Query\Interfaces\IJoin[]
     */
    public array $join = [];

    /**
     * order by.
     *
     * @var \Imi\Db\Query\Interfaces\IOrder[]
     */
    public array $order = [];

    /**
     * group by.
     *
     * @var \Imi\Db\Query\Interfaces\IGroup[]
     */
    public array $group = [];

    /**
     * having.
     *
     * @var \Imi\Db\Query\Interfaces\IHaving[]
     */
    public array $having = [];

    /**
     * 保存的数据.
     *
     * @var array|\Imi\Db\Query\Raw[]|\Imi\Db\Query\Interfaces\IQuery
     */
    public $saveData = [];

    /**
     * 记录从第几个开始取出.
     */
    public ?int $offset = null;

    /**
     * 查询几条记录.
     */
    public ?int $limit = null;

    /**
     * 锁配置.
     *
     * @var int|string|null
     */
    public $lock = null;

    public function __construct()
    {
        $this->table = new Table();
    }

    public function __clone()
    {
        $this->table = clone $this->table;
    }
}
