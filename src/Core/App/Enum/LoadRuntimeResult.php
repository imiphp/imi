<?php

declare(strict_types=1);

namespace Imi\Core\App\Enum;

class LoadRuntimeResult
{
    use \Imi\Util\Traits\TStaticClass;

    public const NONE = 0;

    public const IMI_LOADED = 1;

    public const APP_LOADED = 2;

    public const ALL = self::IMI_LOADED + self::APP_LOADED;
}
