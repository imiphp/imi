<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext\Event;

use Imi\Util\Traits\TStaticClass;

class ConnectionContextEvents
{
    use TStaticClass;

    public const RESTORE = 'IMI.CONNECT_CONTEXT.RESTORE';
}
