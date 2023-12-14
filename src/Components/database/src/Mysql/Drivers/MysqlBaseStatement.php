<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers;

use Imi\Db\Drivers\BaseStatement;
use Imi\Db\Mysql\Contract\IMysqlStatement;

abstract class MysqlBaseStatement extends BaseStatement implements IMysqlStatement
{
}
