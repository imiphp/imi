<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers;

use Imi\Db\Drivers\Base;
use Imi\Db\Mysql\Query\MysqlQuery;
use Imi\Db\Query\Interfaces\IQuery;

abstract class MysqlBase extends Base
{
    /**
     * 创建查询构建器.
     */
    public function createQuery(?string $modelClass = null): IQuery
    {
        return MysqlQuery::newInstance($this, $modelClass, null, null);
    }
}
