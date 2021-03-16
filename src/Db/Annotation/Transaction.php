<?php

declare(strict_types=1);

namespace Imi\Db\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 事务注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Transaction extends Base
{
    /**
     * 数据库连接池名，为null或默认都为默认连接池.
     */
    public ?string $dbPoolName = null;

    /**
     * 事务类型
     * 默认为嵌套.
     */
    public string $type = TransactionType::AUTO;

    /**
     * 自动提交事务
     */
    public bool $autoCommit = true;

    /**
     * 回滚类型
     * 默认为回滚所有.
     */
    public string $rollbackType = RollbackType::ALL;

    /**
     * 回滚层数，默认为1
     * 当 $rollbackType 为 RollbackType::PART 时有效.
     */
    public int $rollbackLevels = 1;

    public function __construct(?array $__data = null, ?string $dbPoolName = null, string $type = TransactionType::AUTO, bool $autoCommit = true, string $rollbackType = RollbackType::ALL, int $rollbackLevels = 1)
    {
        parent::__construct(...\func_get_args());
    }
}
