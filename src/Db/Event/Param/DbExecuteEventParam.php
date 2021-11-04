<?php

declare(strict_types=1);

namespace Imi\Db\Event\Param;

use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Event\EventParam;

class DbExecuteEventParam extends EventParam
{
    /**
     * 数据库对象
     */
    public IDb $db;

    /**
     * 数据库 Statement.
     */
    public ?IStatement $statement = null;

    /**
     * sql 语句.
     */
    public string $sql = '';

    /**
     * 执行开始时间.
     */
    public float $beginTime;

    /**
     * 执行结束时间.
     */
    public float $endTime;

    /**
     * 执行时间.
     */
    public float $time;

    /**
     * 查询绑定的值
     */
    public ?array $bindValues = null;

    /**
     * 执行方法的结果.
     *
     * @var mixed
     */
    public $result;

    /**
     * 执行过程中是否有抛出异常.
     */
    public ?\Throwable $throwable = null;
}
