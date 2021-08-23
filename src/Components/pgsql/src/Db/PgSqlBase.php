<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db;

use Imi\Db\Drivers\Base;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Pgsql\Db\Query\PgsqlQuery;

abstract class PgSqlBase extends Base
{
    /**
     * 创建查询构建器.
     */
    public function createQuery(?string $modelClass = null): IQuery
    {
        return PgsqlQuery::newInstance($this, $modelClass, null, null);
    }
}
