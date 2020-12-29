<?php

namespace Imi\Db\Event\Param;

use Imi\Event\EventParam;

class DbPrepareEventParam extends EventParam
{
    /**
     * 数据库 Statement.
     *
     * @var \Imi\Db\Interfaces\IStatement
     */
    public $statement;

    /**
     * sql 语句.
     *
     * @var string
     */
    public $sql;
}
