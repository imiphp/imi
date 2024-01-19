<?php

declare(strict_types=1);

namespace Imi\Db\Drivers\Consts;

use Imi\Util\Traits\TStaticClass;

class ParamType
{
    use TStaticClass;

    public const PARAM_AUTO = -1;

    public const PARAM_NULL = 0;

    public const PARAM_INT = 1;

    public const PARAM_STR = 2;

    public const PARAM_LOB = 3;

    public const PARAM_BOOL = 5;
}
