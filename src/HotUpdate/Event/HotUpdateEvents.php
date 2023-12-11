<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Event;

use Imi\Util\Traits\TStaticClass;

final class HotUpdateEvents
{
    use TStaticClass;

    public const BEGIN_BUILD = 'imi.hotupdate.begin_build';
}
