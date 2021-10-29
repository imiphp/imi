<?php

declare(strict_types=1);

namespace Imi\Db\Event\Param;

use Imi\Db\Interfaces\IStatement;
use Imi\Event\EventParam;

class DbPrepareEventParam extends EventParam
{
    /**
     * 数据库 Statement.
     */
    public ?IStatement $statement = null;

    /**
     * sql 语句.
     */
    public string $sql = '';

    /**
     * 执行过程中是否有抛出异常.
     */
    public ?\Throwable $throwable = null;
}
