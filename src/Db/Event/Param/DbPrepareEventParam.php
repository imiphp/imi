<?php

declare(strict_types=1);

namespace Imi\Db\Event\Param;

use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Event\CommonEvent;

class DbPrepareEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 数据库对象
         */
        public readonly ?IDb $db = null,

        /**
         * 数据库 Statement.
         */
        public readonly ?IStatement $statement = null,

        /**
         * sql 语句.
         */
        public readonly string $sql = '',

        /**
         * 执行过程中是否有抛出异常.
         */
        public readonly ?\Throwable $throwable = null
    ) {
        parent::__construct('IMI.DB.PREPARE', $db);
    }
}
