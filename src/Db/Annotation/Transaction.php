<?php
namespace Imi\Db\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Db\Annotation\RollbackType;

/**
 * 列字段注解
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Db\Parser\DbParser")
 */
class Transaction extends Base
{
    /**
     * 数据库连接池名，为null或默认都为默认连接池
     *
     * @var string|null
     */
    public $dbPoolName = null;

    /**
     * 事务类型
     * 默认为嵌套
     *
     * @var string
     */
    public $type = TransactionType::NESTING;

    /**
     * 自动提交事务
     *
     * @var boolean
     */
    public $autoCommit = true;

    /**
     * 回滚类型
     * 默认为回滚所有
     *
     * @var string
     */
    public $rollbackType = RollbackType::ALL;

    /**
     * 回滚层数，默认为1
     * 当 $rollbackType 为 RollbackType::PART 时有效
     *
     * @var integer
     */
    public $rollbackLevels = 1;
}