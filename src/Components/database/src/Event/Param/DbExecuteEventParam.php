<?php

declare(strict_types=1);

namespace Imi\Db\Event\Param;

use Imi\Db\Event\DbEvents;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Event\CommonEvent;

class DbExecuteEventParam extends CommonEvent
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
         * 执行开始时间.
         */
        public readonly float $beginTime = 0,

        /**
         * 执行结束时间.
         */
        public readonly float $endTime = 0,

        /**
         * 执行时间.
         */
        public readonly float $time = 0,

        /**
         * 查询绑定的值
         */
        public readonly ?array $bindValues = null,

        /**
         * 执行方法的结果.
         */
        public readonly mixed $result = null,

        /**
         * 执行过程中是否有抛出异常.
         */
        public readonly ?\Throwable $throwable = null
    ) {
        parent::__construct(DbEvents::EXECUTE, $db);
    }
}
