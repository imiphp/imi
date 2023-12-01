<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Event;

use Imi\Util\Traits\TStaticClass;

final class HotUpdateEvents
{
    use TStaticClass;

    public const BEGIN_BUILD = 'IMI.HOTUPDATE.BEGIN_BUILD';
}
