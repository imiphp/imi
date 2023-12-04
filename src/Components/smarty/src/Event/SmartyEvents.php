<?php

declare(strict_types=1);

namespace Imi\Smarty\Event;

use Imi\Util\Traits\TStaticClass;

final class SmartyEvents
{
    use TStaticClass;

    public const NEW_SMARTY = 'imi.smarty.new';
}
