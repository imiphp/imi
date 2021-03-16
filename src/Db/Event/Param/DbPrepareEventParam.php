<?php

namespace Imi\Db\Event\Param;

use Imi\Db\Interfaces\IStatement;
use Imi\Event\EventParam;

class DbPrepareEventParam extends EventParam
{
    /**
     * 数据库 Statement.
     */
    public IStatement $statement;

    /**
     * sql 语句.
     */
    public string $sql = '';
}
