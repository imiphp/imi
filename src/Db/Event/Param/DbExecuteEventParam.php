<?php

namespace Imi\Db\Event\Param;

use Imi\Event\EventParam;

class DbExecuteEventParam extends EventParam
{
    /**
     * 数据库对象
     *
     * @var \Imi\Db\Interfaces\IDb
     */
    public $db;

    /**
     * 数据库 Statement.
     *
     * @var \Imi\Db\Interfaces\IStatement|null
     */
    public $statement = null;

    /**
     * sql 语句.
     *
     * @var string
     */
    public $sql;

    /**
     * 执行开始时间.
     *
     * @var float
     */
    public $beginTime;

    /**
     * 执行结束时间.
     *
     * @var float
     */
    public $endTime;

    /**
     * 执行时间.
     *
     * @var float
     */
    public $time;

    /**
     * 查询绑定的值
     *
     * @var array|null
     */
    public $bindValues = null;

    /**
     * 执行方法的结果.
     *
     * @var mixed
     */
    public $result;
}
