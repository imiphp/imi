<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\PdoPgsql;

use Imi\Db\Drivers\TPdoStatement;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBaseStatement;

/**
 * PDO Pgsql驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends PgsqlBaseStatement implements IPgsqlStatement
{
    use TPdoStatement;
}
