<?php

namespace Imi\Db\Event\Param;

use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Event\EventParam;

class DbExecuteEventParam extends EventParam
{
    /**
     * 数据库对象
     *
     * @var \Imi\Db\Interfaces\IDb
     */
    public IDb $db;

    /**
     * 数据库 Statement.
     *
     * @var \Imi\Db\Interfaces\IStatement|null
     */
    public ?IStatement $statement = null;

    /**
     * sql 语句.
     *
     * @var string
     */
    public string $sql = '';

    /**
     * 执行开始时间.
     *
     * @var float
     */
    public float $beginTime;

    /**
     * 执行结束时间.
     *
     * @var float
     */
    public float $endTime;

    /**
     * 执行时间.
     *
     * @var float
     */
    public float $time;

    /**
     * 查询绑定的值
     *
     * @var array|null
     */
    public ?array $bindValues = null;

    /**
     * 执行方法的结果.
     *
     * @var mixed
     */
    public $result;
}
