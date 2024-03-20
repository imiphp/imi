<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Table\Event\Param;

trait TGenerateTable
{
    public function __construct(
        /**
         * 模型类名.
         */
        public string $className,

        /**
         * 表名.
         */
        public string $tableName,

        /**
         * 是否跳过.
         */
        public bool $skip,

        /**
         * DDL 语句.
         */
        public string $ddl,
    ) {
        parent::__construct(static::class);
    }
}
