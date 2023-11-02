<?php

declare(strict_types=1);

namespace Imi\Db\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 事务注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Transaction extends Base
{
    public function __construct(
        /**
         * 数据库连接池名，为null或默认都为默认连接池.
         */
        public ?string $dbPoolName = null,
        /**
         * 事务类型；默认为嵌套.
         */
        public string $type = TransactionType::AUTO,
        /**
         * 自动提交事务
         */
        public bool $autoCommit = true,
        /**
         * 回滚类型；默认为回滚所有；回滚部分通常配合type=TransactionType::NESTING使用.
         */
        public string $rollbackType = RollbackType::ALL,
        /**
         * 回滚层数，默认为1；当 $rollbackType 为 RollbackType::PART 时有效。设为null则全部回滚.
         */
        public ?int $rollbackLevels = 1
    ) {
    }
}
