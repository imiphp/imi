<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\PdoMysql;

use Imi\Db\Drivers\TPdoStatement;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBaseStatement;

/**
 * PDO MySQL驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends MysqlBaseStatement implements IMysqlStatement
{
    use TPdoStatement;

    /**
     * 检查是否离线的错误码索引.
     *
     * @readonly
     */
    protected int $checkIsOfflineCodeIndex = 1;
}
