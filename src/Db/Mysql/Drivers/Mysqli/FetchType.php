<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\Mysqli;

use Imi\Util\Traits\TStaticClass;

class FetchType
{
    use TStaticClass;

    public const FETCH_ASSOC = 2;

    public const FETCH_NUM = 3;

    public const FETCH_BOTH = 4;

    public const FETCH_OBJ = 5;
}
