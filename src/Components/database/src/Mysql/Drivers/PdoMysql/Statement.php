<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\PdoMysql;

use Imi\Db\Drivers\PDO\TPDOStatement;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBaseStatement;

/**
 * PDO MySQL驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends MysqlBaseStatement implements IMysqlStatement
{
    use TPDOStatement;
}
