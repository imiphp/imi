<?php

namespace Imi\Db\Event\Param;

use Imi\Db\Interfaces\IStatement;
use Imi\Event\EventParam;

class DbPrepareEventParam extends EventParam
{
    /**
     * 数据库 Statement.
     *
     * @var \Imi\Db\Interfaces\IStatement
     */
    public IStatement $statement;

    /**
     * sql 语句.
     *
     * @var string
     */
    public string $sql = '';
}
