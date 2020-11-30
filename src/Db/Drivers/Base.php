<?php

declare(strict_types=1);

namespace Imi\Db\Drivers;

use Imi\Db\Interfaces\IDb;
use Imi\Util\Traits\THashCode;

abstract class Base implements IDb
{
    use THashCode;
}
